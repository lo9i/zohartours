
from datetime import date, datetime, timedelta
from decimal import Decimal

from db import db, Search, User, Hotel, Room, Date
from util import make_calendar, create_range
from .promo_core import PromoCore
    

class ZoharTours(object):

    @staticmethod
    def find_hotels_for_search(search: Search, locale: str='en'):
        
        zohartours = ZoharTours(search, locale)
        result = zohartours.find_hotels()
        return result

    def __init__(self, search, locale):
        self._search = search
        self._user = self._search.user
        self._today = date.today()
        self._check_in = search.check_in
        self._check_out = search.check_out
        self._nights = search.nights
        self._days = create_range(self._check_in.date(), self._check_out.date())
        self._days_until_check_in = self._get_days_until_check_in(self._check_in)
        self._locale = locale
        self._pax = 0
        self._percent_commission = 0

    def find_hotels(self):
        if self._days_until_check_in < 0:
            return []

        self._hotels = self._get_enabled_hotels_that_match_criteria()
        if not self._hotels:
            return []

        adults, children = self._search.room_1_adults, self._search.room_1_children
        children_age = int(self._search.room_1_children_age[children])
        self._percent_commission = (
            self._user.agency.commission if self._user.agency else 0
        )
        self._pax = adults + children
        matched_hotels = []
        for hotel in self._hotels:
            matched_hotel = self._check_if_hotel_applies(hotel, adults, children, children_age)
            if matched_hotel:
                matched_hotels.append(matched_hotel)
        # matched_hotels.sort(key=lambda h: (h["isBlackedOut"], h["lowPrice"]))
        
        """  
        Transformar la lista de hoteles en dictionaries
        """

        final_hotels = []
        for h in matched_hotels:
            h = self._strip_hotel(h)
            final_hotels.append(h)
        return final_hotels

    def _strip_mandatory_service(self, service):
        return {
            "name": str(getattr(service, "name", "")),
            "description": str(getattr(service, "description", ""))
        }

    def _strip_room(self, room):
        del room['stayDates']
        del room['dates']
        room["cancellableUntil"] = room["cancellableUntil"].strftime('%Y-%m-%d %H:%M:%S')
        try:
            del room['priceNet']
            del room['taxesNet']
            del room['totalServicesNet']
            del room['totalWithServicesNet']
        except KeyError:
            pass
        room["price"] = str(room["price"])
        room["taxes"] = str(room["taxes"])
        room["commission"] = str(room["commission"])
        room["total"] = str(room["total"])
        room["totalWithMandatoryServices"] = str(room["totalWithMandatoryServices"])
        room["mandatoryServicesTotal"] = str(room["mandatoryServicesTotal"])
        del room['lowPrice']
        del room['cancellationPrice']
        for i in range(0, len(room["mandatoryServices"])):
            mandatory_service = room["mandatoryServices"][i]
            room["mandatoryServices"][i] = self._strip_mandatory_service(mandatory_service)

            # room.calendar ----> éste no lo encontré
        return room

    def _strip_hotel(self, hotel):
        del hotel['hotel']
        hotel["is_beds_hotel"] = False
        hotel["stars"] = str(hotel["stars"])
        hotel["checkIn"] = hotel["checkIn"].strftime('%H:%M:%S')
        hotel["checkOut"] = hotel["checkOut"].strftime('%H:%M:%S')
        hotel["mandatoryServicesPrice"] = str(hotel["mandatoryServicesPrice"])
        hotel["lowPrice"] = str(hotel["lowPrice"])
        hotel["cancellationCutOff"] = str(hotel["cancellationCutOff"])
        for i in range(0, len(hotel["rooms"])):
            room = hotel["rooms"][i]
            hotel["rooms"][i] = self._strip_room(room)
        return hotel

    def _get_days_until_check_in(self, check_in: date) -> int:
        return (check_in.date() - self._today).days

    def _get_enabled_hotels_that_match_criteria(self):
        query = db.session.query(Hotel).filter_by(enabled=True)
        
        if self._search.continent:
            query = query.filter_by(continent=self._search.continent)
        
        if self._search.country:
            query = query.filter_by(country=self._search.country)
        
        if self._search.state:
            query = query.filter_by(state=self._search.state)
        
        if self._search.city:
            query = query.filter_by(city=self._search.city)
        
        if self._search.hotel:
            query = query.filter_by(name=self._search.hotel)
        
        if not self._search.user.vip:
            query = query.filter_by(vip=False)
            
        return query.all()

    def _check_if_hotel_applies(self, hotel, adults, children, children_age):
        rooms = (
            db.session.query(Room)
            .filter(Room.hotel_id == hotel.id, Room.capacity >= self._pax)
            .all()
        )
        if not rooms:
            return None

        matched_hotel = self._build_matched_hotel(hotel, adults, children, children_age)
        matched_rooms = []
        for room in rooms:
            matched_room = self._check_if_room_applies(matched_hotel, room)
            if not matched_room:
                continue

            matched_rooms.append(matched_room)

            # matched_promo_rooms = self._check_if_room_has_promotions(
            #     matched_hotel, room, matched_room["dates"]
            # )
            # if not matched_promo_rooms:
            #     continue

            # matched_rooms.extend(matched_promo_rooms)

        if not matched_rooms:
            return None

        matched_rooms.sort(
            key=lambda r: (r["isBlackedOut"], r["totalWithMandatoryServices"])
        )
        matched_hotel["rooms"] = matched_rooms
        self._calculate_min_price_for_hotel(matched_hotel)
        return matched_hotel

    def _check_if_room_applies(self, matched_hotel, room):

        dates_entities = db.session.query(Date).filter(
                Date.room_id == room.id,
                Date.minimum_stay <= self._nights,
                Date.maximum_stay >= self._nights,
                Date.date.in_([date.strftime("%Y-%m-%d 00:00:00") for date in self._days]),
            ).all()
        
        if not dates_entities:
            return None

        matched_room = self._build_matched_room(matched_hotel, room, dates_entities)
        self._calculate_dates_amounts(matched_room)
        self._calculate_totals_for_room(matched_hotel, matched_room)
        return matched_room

    def _check_if_room_has_promotions(
        self,
        matched_hotel,
        room,
        dates_entities,
    ):
        if not dates_entities:
            return None

        if not room.combinable_promotions and not room.non_combinable_promotions:
            return None

        matched_room = self._build_matched_room(matched_hotel, room, dates_entities)
        room_with_promotions = self._apply_promotions(
            matched_room, room.combinable_promotions, room.non_combinable_promotions
        )

        if not room_with_promotions:
            return None

        for room_with_promotion in room_with_promotions:
            self._calculate_dates_amounts(room_with_promotion)
            self._calculate_totals_for_room(matched_hotel, room_with_promotion)

        return room_with_promotions

    def _apply_promotions(
        self, matched_room, combinable_promotions, non_combinable_promotions
    ):
        promo_core = PromoCore(
            self._search, matched_room, combinable_promotions, non_combinable_promotions
        )
        promos = promo_core.find_promotions()

        if not promos:
            return []

        rooms_with_promos = []
        for promo_room in promos:
            if not promo_room:
                continue

            promo_room["promoIds"] = ",".join(map(str, promo_room["promoIds"]))
            rooms_with_promos.append(promo_room)

        return rooms_with_promos

    def _calculate_dates_amounts(self, matched_room: dict) -> None:
        for stay_date in matched_room["stayDates"]:
            self._calculate_single_date_amounts(stay_date)

    def _calculate_single_date_amounts(self, stay_date: dict) -> None:
        if stay_date["isBlackedOut"]:
            stay_date["price"] = round(0, 2)
            stay_date["tax"] = round(0, 2)
            stay_date["commission"] = round(0, 2)
            stay_date["total"] = round(0, 2)
            stay_date["totalWithServices"] = round(0, 2)
            stay_date["totalWithServicesAndCommission"] = round(0, 2)
            return

        night_room_price = stay_date["priceNet"]
        night_tax = (night_room_price * stay_date["nightTax"]) + stay_date[
            "nightOccupancyTax"
        ]
        night_tax = Decimal(night_tax)
        night_price_and_taxes = night_room_price + night_tax
        night_price_and_taxes = Decimal(night_price_and_taxes)
        profit_type_slug = stay_date["profitType"]
        night_price_with_tax_and_profit = night_price_and_taxes + (
            stay_date["profit"]
            if profit_type_slug == "net"
            else night_price_and_taxes * (stay_date["profit"] / 100)
        )
        night_price_with_tax_and_profit = Decimal(night_price_with_tax_and_profit)
        commission = night_price_with_tax_and_profit * (
            stay_date["agencyCommission"] / 100
        )
        price_night_to_show = night_price_with_tax_and_profit - night_tax
        total_price_with_services = (
            night_price_with_tax_and_profit + stay_date["mandatoryServices"]
        )
        total_price_with_services_and_commission = (
            total_price_with_services - commission
        )
        total_price_with_services_net = (
            night_price_and_taxes + stay_date["mandatoryServices"]
        )

        stay_date["price"] = round(price_night_to_show, 2)
        stay_date["tax"] = round(night_tax, 2)
        stay_date["total"] = round(night_price_with_tax_and_profit, 2)
        stay_date["totalWithServices"] = round(total_price_with_services, 2)
        stay_date["totalWithServicesAndCommission"] = round(
            total_price_with_services_and_commission, 2
        )

        # Our Final cost
        stay_date["taxNet"] = round(night_tax, 2)
        stay_date["totalWithServicesNet"] = total_price_with_services_net

        if stay_date["isPromotion"]:
            stay_date["priceWODiscount"] = stay_date["price"]
            stay_date["totalWODiscount"] = stay_date["total"]
            stay_date["taxWODiscount"] = stay_date["tax"]

            if stay_date["isFree"]:
                # If it is free, no taxes
                night_tax = 0
                price_night_to_show = 0
                commission = 0
                night_price_with_tax_and_profit = 0
                total_price_with_services = 0
                total_price_with_services_and_commission = stay_date[
                    "mandatoryServicesRemaining"
                ]
                total_price_with_services_net = stay_date["mandatoryServicesRemaining"]
            else:
                night_room_price = (
                    stay_date["priceNetRemaining"] + stay_date["priceExtraPeople"]
                )
                night_tax = (night_room_price * stay_date["nightTax"]) + stay_date[
                    "nightOccupancyTax"
                ]
                night_price_and_taxes = night_room_price + night_tax
                profit_type_slug = stay_date["profitType"]
                night_price_with_tax_and_profit = night_price_and_taxes + (
                    stay_date["profit"]
                    if profit_type_slug == "net"
                    else night_price_and_taxes * (stay_date["profit"] / 100)
                )

                commission = night_price_with_tax_and_profit * (
                    stay_date["agencyCommission"] / 100
                )
                price_night_to_show = night_price_with_tax_and_profit - night_tax
                total_price_with_services = (
                    night_price_with_tax_and_profit
                    + stay_date["mandatoryServicesRemaining"]
                )
                total_price_with_services_and_commission = (
                    total_price_with_services - commission
                )
                total_price_with_services_net = (
                    night_price_and_taxes + stay_date["mandatoryServicesRemaining"]
                )

            stay_date["tax"] = round(night_tax, 2)
            stay_date["price"] = round(price_night_to_show, 2)
            stay_date["commission"] = round(commission, 2)
            stay_date["total"] = round(night_price_with_tax_and_profit, 2)
            stay_date["totalWithServices"] = round(total_price_with_services, 2)
            stay_date["totalWithServicesAndCommission"] = round(
                total_price_with_services_and_commission, 2
            )

            # Our Final cost with discounts applied
            stay_date["taxNet"] = round(night_tax, 2)
            stay_date["totalWithServicesNet"] = total_price_with_services_net

    def _calculate_min_price_for_hotel(self, matched_hotel):
        matched_hotel["lowPrice"] = min(
            (r["totalWithMandatoryServices"] for r in matched_hotel["rooms"]), default=0
        )

    def _calculate_totals_for_room(
        self, matched_hotel, matched_room, use_net_values=False
    ):
        # Costs
        stay_total_net = Decimal(0).quantize(Decimal("0.00"))
        stay_total_services_net = Decimal(0).quantize(Decimal("0.00"))
        stay_total_with_services_net = Decimal(0).quantize(Decimal("0.00"))

        # If there are no discounts they are the same.
        # If any discount is applied they differ
        stay_tax_net = Decimal(0).quantize(Decimal("0.00"))

        room_cancellation_policy_type = ""
        room_cancellation_policy_cutoff = 0
        room_cancellation_policy_penalty = Decimal(0).quantize(Decimal("0.00"))
        room_on_request = False
        room_blacked_out = False
        room_is_cancelable = matched_room["isCancellable"]
        stay_total = Decimal(0).quantize(Decimal("0.00"))
        stay_price = Decimal(0).quantize(Decimal("0.00"))
        stay_taxes = Decimal(0).quantize(Decimal("0.00"))
        stay_commission = Decimal(0).quantize(Decimal("0.00"))
        stay_total_with_mandatory = Decimal(0).quantize(Decimal("0.00"))
        stay_mandatory = Decimal(0).quantize(Decimal("0.00"))
        room_max_price = Decimal(0).quantize(Decimal("0.00"))
        room_low_price = Decimal(0).quantize(Decimal("0.00"))
        hotel_checkin = matched_hotel["checkIn"]
        room_days_until_checkin = self._get_days_until_check_in(self._check_in)
        cancellable_until = datetime.today()
        cancellable_set = False

        for stay_date in matched_room["stayDates"]:
            if stay_date["onRequest"]:
                room_on_request = True

            if stay_date["isBlackedOut"]:
                room_blacked_out = True
                room_on_request = True
                continue

            room_low_price = self._get_lower_price(room_low_price, stay_date["total"])
            room_max_price = max(
                room_max_price, stay_date["totalWithServicesAndCommission"]
            )

            if room_cancellation_policy_cutoff == 0 and stay_date["cutOff"] > 0:
                room_cancellation_policy_cutoff = stay_date["cutOff"]
                room_cancellation_policy_type = stay_date["cutOffType"]
                room_cancellation_policy_penalty = stay_date["cancellationPenalty"]

            is_cancellable = room_days_until_checkin >= stay_date["cutOff"]

            if room_is_cancelable and is_cancellable:
                if not cancellable_set:  # Only care about the first day
                    cancellable_set = True
                    cancellable_until = self._check_in.replace(hour=hotel_checkin.hour, minute=hotel_checkin.minute, second=hotel_checkin.second, microsecond=hotel_checkin.microsecond)
                    cancellable_until -= timedelta(days=room_cancellation_policy_cutoff)
            else:
                room_is_cancelable = False

            price_to_add = stay_date["price"]
            tax_to_add = stay_date["tax"]

            if matched_room["isPromotion"] and stay_date.get("dateWithDiscount"):
                if stay_date["totalWODiscount"] != stay_date["total"]:
                    matched_room["promoDiscount"] += (
                        stay_date["totalWODiscount"] - stay_date["total"]
                    )

                the_price = (
                    stay_date["priceNetRemaining"] + stay_date["priceExtraPeople"]
                )
                if stay_date["priceNet"] != the_price:
                    matched_room["promoDiscountNet"] += (
                        stay_date["priceNet"] - the_price
                    )

                if (
                    stay_date["mandatoryServicesRemaining"]
                    != stay_date["mandatoryServices"]
                ):
                    matched_room["promoDiscount"] += (
                        stay_date["mandatoryServices"]
                        - stay_date["mandatoryServicesRemaining"]
                    )
                    matched_room["promoDiscountNet"] += (
                        stay_date["mandatoryServices"]
                        - stay_date["mandatoryServicesRemaining"]
                    )

                price_to_add = stay_date["priceWODiscount"]
                tax_to_add = stay_date["taxWODiscount"]

            # Room costs
            stay_total_net += stay_date["priceNet"]
            stay_total_services_net += stay_date["mandatoryServices"]
            stay_total_with_services_net += stay_date["totalWithServicesNet"]
            stay_tax_net += stay_date["taxNet"]

            stay_taxes += tax_to_add
            stay_price += price_to_add
            stay_total += stay_date["total"]
            stay_commission += stay_date["commission"]
            stay_mandatory += stay_date["mandatoryServicesRemaining"]
            stay_total_with_mandatory += stay_date["totalWithServicesAndCommission"]

        matched_room["isBlackedOut"] = room_blacked_out
        matched_room["isCancellable"] = room_is_cancelable
        matched_room["calendar"] = make_calendar(
            matched_room["stayDates"], self._nights, use_net_values
        )
        matched_room["cancellableUntil"] = cancellable_until
        matched_room["onRequest"] = room_on_request

        if room_blacked_out:
            matched_room["taxes"] = Decimal(0).quantize(Decimal("0.00"))
            matched_room["price"] = Decimal(0).quantize(Decimal("0.00"))
            matched_room["commission"] = Decimal(0).quantize(Decimal("0.00"))
            matched_room["total"] = Decimal(0).quantize(Decimal("0.00"))
            matched_room["totalWithMandatoryServices"] = Decimal(0).quantize(
                Decimal("0.00")
            )
            matched_room["mandatoryServicesTotal"] = Decimal(0).quantize(
                Decimal("0.00")
            )
            matched_room["lowPrice"] = Decimal(0).quantize(Decimal("0.00"))
            matched_room["cancellationPrice"] = Decimal(0).quantize(Decimal("0.00"))
        else:
            if matched_room["isCancellable"]:
                if room_cancellation_policy_type == "nights":
                    stay_cancellation_price = room_max_price * min(
                        room_cancellation_policy_penalty, self._nights
                    )
                elif room_cancellation_policy_type == "net":
                    stay_cancellation_price = room_cancellation_policy_penalty
                elif room_cancellation_policy_type == "percent":
                    stay_cancellation_price = stay_total * (
                        room_cancellation_policy_penalty / 100
                    )
                else:
                    raise ValueError("Invalid cancellation policy")
            else:
                stay_cancellation_price = stay_total

            # Room cost details
            matched_room["priceNet"] = stay_total_net.quantize(Decimal("0.00"))
            matched_room["taxesNet"] = stay_tax_net.quantize(Decimal("0.00"))
            matched_room["totalServicesNet"] = stay_total_services_net.quantize(
                Decimal("0.00")
            )
            matched_room["totalWithServicesNet"] = (
                stay_total_with_services_net.quantize(Decimal("0.00"))
            )

            # Prices for the agency
            matched_room["price"] = stay_price.quantize(Decimal("0.00"))
            matched_room["taxes"] = stay_taxes.quantize(Decimal("0.00"))
            matched_room["commission"] = stay_commission.quantize(Decimal("0.00"))
            matched_room["total"] = stay_total.quantize(Decimal("0.00"))
            matched_room["totalWithMandatoryServices"] = (
                stay_total_with_mandatory.quantize(Decimal("0.00"))
            )
            matched_room["mandatoryServicesTotal"] = stay_mandatory.quantize(
                Decimal("0.00")
            )
            matched_room["lowPrice"] = room_low_price.quantize(Decimal("0.00"))
            matched_room["cancellationPrice"] = stay_cancellation_price.quantize(
                Decimal("0.00")
            )

    def _get_lower_price(self, low_price, night_price):
        return low_price if night_price > low_price > 0 else night_price

    def _build_matched_hotel(self, hotel, adults, children, children_age):
        if children:
            real_adults = hotel.get_real_adults(adults, children_age)
            real_children = self._pax - real_adults
        else:
            real_adults = adults
            real_children = 0

        return {
            "id": hotel.id,
            "hotel": hotel,
            "name": hotel.name,
            "address": hotel.address,
            "coords": hotel.coords,
            "subtitle": hotel.subtitle,
            "stars": hotel.stars,
            "city": hotel.city,
            "state": hotel.state,
            "checkIn": hotel.check_in,
            "checkOut": hotel.check_out,
            "childAge": hotel.child_age,
            "currency": hotel.currency.symbol,
            # "images": hotel.images,
            "mandatoryServicesPrice": hotel.get_mandatory_services_total(
                self._pax, self._nights
            ),
            "rooms": [],
            "adults": real_adults,
            "children": real_children,
            "lowPrice": 0,
            "cancellableUntil": "NOT SET",
            "cancellationCutOff": 0,
            "hasPromotion": False,
            "isBlackedOut": False,
        }

    def _build_matched_room(self, matched_hotel, room, dates_entities):

        # Get room's mandatory services and add them to the hotel's
        hotel_plus_room_mandatory_services_price_per_stay = matched_hotel[
            "mandatoryServicesPrice"
        ] + room.get_mandatory_services_total(self._pax, self._nights)
        night_mandatory_services_price = (
            hotel_plus_room_mandatory_services_price_per_stay / self._nights
        )
        mandatory_services = room.hotel.get_mandatory_services() + room.get_mandatory_services()
        return {
            "id": room.id,
            "name": room.name,
            "capacity": room.capacity,
            "currency": matched_hotel["currency"],
            "stayDates": self._build_stay_dates(
                dates_entities, night_mandatory_services_price
            ),
            "dates": dates_entities,
            "mandatoryServices": mandatory_services,
            "onRequest": False,
            "isBlackedOut": False,
            "isPromotion": False,
            "isCancellable": True,
            "cancellableUntil": self._today,
            "adults": matched_hotel["adults"],
            "children": matched_hotel["children"],
            "description": room.get_description(self._locale),
        }

    def _build_stay_dates(self, dates_entities, night_mandatory_services):
        return [
            self._build_single_stay_date(date_entity, night_mandatory_services)
            for date_entity in dates_entities
        ]

    def _build_single_stay_date(self, the_date, night_mandatory_services):
        rate = the_date.get_rate()
        price_net = rate.get_price(self._pax)
        price_base = rate.get_price()
        extra_people = price_net - price_base

        return {
            "id": the_date.id,
            "date": the_date,
            "day": the_date.date.day,
            "nightTax": Decimal(rate.tax / 100),
            "nightOccupancyTax": Decimal(rate.occupancy_tax),
            "priceNetRemaining": Decimal(price_base),
            "priceNet": Decimal(price_net),
            "priceExtraPeople": Decimal(extra_people),
            "mandatoryServices": night_mandatory_services,
            "mandatoryServicesRemaining": night_mandatory_services,
            "profit": Decimal(rate.profit),
            "profitType": rate.profit_type.slug,
            "agencyCommission": self._percent_commission,
            "cutOff": the_date.cancellation_policy.cut_off,
            "cutOffType": the_date.cancellation_policy.cancellation_policy_type.slug,
            "cancellationPenalty": the_date.cancellation_policy.penalty,
            "isBlackedOut": rate.price == 0,
            "isPremium": the_date.is_premium_date,
            "onRequest": self._check_if_date_is_on_request(the_date),
            "isPromotion": False,
            # Calculated values
            "totalNet": round(0, 2),
            "totalWithServicesNet": round(0, 2),
            "price": round(0, 2),
            "tax": round(0, 2),
            "commission": round(0, 2),
            "total": round(0, 2),
            "dateWithDiscount": False,
            "isObject": False,
            "isFree": False,
            "totalWithServices": round(0, 2),
            "totalWithServicesAndCommission": round(0, 2),
        }

    def _check_if_date_is_on_request(self, the_date):
        return (
            self._is_inside_cut_off(the_date)
            or the_date.stop_sell
            or self._nights < the_date.minimum_stay
            or self._nights > the_date.maximum_stay
            or the_date.stock < 1
        )

    def _is_inside_cut_off(self, the_date):
        today = datetime.today()
        days_until_check_in = abs((the_date.date - today).days)
        return days_until_check_in < the_date.cut_off
