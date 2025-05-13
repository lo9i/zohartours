from datetime import datetime
from decimal import Decimal


class PromoCore(object):

    def __init__(self, search, room, combinable_promotions=None, non_combinable_promotions=None):
        self._today = datetime.today()
        self._combinable_promotions = combinable_promotions or []
        self._non_combinable_promotions = non_combinable_promotions or []
        self._search = search
        self._room = room
        self.pax = self._room['adults'] + self._room['children']
        self._days_until_check_in = (self._search.check_in - self._today).days

    def find_promotions(self):
        rooms_with_promotions = []
        combinable_room = self._room.copy()

        for combinable_promotion in self._combinable_promotions:
            if (self._check_applies_period(combinable_promotion) and
                    self._check_validity_period(combinable_promotion) and
                    self.check_conditions(combinable_promotion) and
                    self._check_cut_off(combinable_promotion)):
                modified_combinable_room = self._apply_benefits(combinable_room, combinable_promotion)
                if modified_combinable_room:
                    combinable_room = modified_combinable_room

        if combinable_room and combinable_room != self._room:
            rooms_with_promotions.append(combinable_room)

        for non_combinable_promotion in self._non_combinable_promotions:
            if (self._check_applies_period(non_combinable_promotion) and
                    self._check_validity_period(non_combinable_promotion) and
                    self.check_conditions(non_combinable_promotion) and
                    self._check_cut_off(non_combinable_promotion)):
                rooms_with_promotions.append(self._apply_benefits(self._room, non_combinable_promotion))

        return rooms_with_promotions

    def check_conditions(self, promotion):
        conditions = promotion.get_comparation_conditions()
        if not conditions:
            return True

        start = max(self._search.check_in, promotion.period_from)
        end = min(self._search.check_out, promotion.period_to)
        nights = (end - start).days

        conditional = {
            'nights': nights,
            'adults': self._search.room1_adults,
            'children': self._search.room1_children,
            'rooms': self._search.rooms,
            'pax': self._search.room1_adults + self._search.room1_children
        }

        for condition in conditions:
            condition_expression = condition.expression.slug
            if condition_expression == 'for-each':
                continue

            condition_conditional = condition.conditional.slug
            condition_value = condition.value

            if_condition = {
                'equal': conditional[condition_conditional] == condition_value,
                'not-equal': conditional[condition_conditional] != condition_value,
                'less-than': conditional[condition_conditional] < condition_value,
                'less-than-or-equal': conditional[condition_conditional] <= condition_value,
                'greater-than': conditional[condition_conditional] > condition_value,
                'greater-than-or-equal': conditional[condition_conditional] >= condition_value
            }

            if not if_condition.get(condition_expression, False):
                return False

        return True

    def _check_cut_off(self, promotion):
        return promotion.cut_off <= 0 or self._days_until_check_in >= promotion.cut_off

    def _check_validity_period(self, promotion):
        return promotion.valid_from <= self._today <= promotion.valid_to

    def _check_applies_period(self, promotion):
        return self._search.check_in <= promotion.period_to and self._search.check_out >= promotion.period_from

    def _apply_benefits(self, room, promotion):
        stay_dates = room['stayDates']
        promo_benefits = promotion.benefits
        promo_available_days = promotion.get_available_days()
        promo_exceptions = promotion.exceptions
        available_dates_keys = self._available_dates_keys_func(stay_dates, promo_available_days, promotion)
        available_dates_count = len(available_dates_keys)

        if available_dates_count <= 0 or not promo_benefits:
            return []

        promotion_code = f"{room['promotion']} + {promotion.name}" if 'promotion' in room else promotion.name
        benefits_data = {
            'promoCode': promotion_code,
            'daysCount': available_dates_count,
            'dates': room['dates'],
            'stayDates': stay_dates,
            'datesKeys': available_dates_keys,
            'anyDiscountApplied': False
        }

        for benefit in promo_benefits:
            benefit_type = benefit.type.slug
            if benefit_type == 'stay-discount':
                self._apply_stay_discount(benefits_data, benefit, promo_exceptions)
            elif benefit_type == 'night-discount':
                self._apply_night_discount(benefits_data, benefit, promo_exceptions)
            elif benefit_type in ['room-service-discount', 'hotel-service-discount']:
                self._apply_service_discount(benefits_data, benefit, promo_exceptions)
            else:
                raise Exception("Unknown discount type")

        if not benefits_data['anyDiscountApplied']:
            return []

        promo_ids = room.get('promoIds', [])
        promo_ids.append(promotion.id)
        room_promotion_push = {
            'stayDates': benefits_data['stayDates'],
            'promotion': promotion_code,
            'isPromotion': True,
            'onRequest': False,  # Placeholder for SearchCore logic
            'promoDiscount': 0,
            'promoDiscountNet': 0,
            'promoIds': promo_ids,
            'isCancellable': not promotion.non_refundable and room.get('isCancellable', True)
        }

        return {**room, **room_promotion_push}

    def _available_dates_keys_func(self, stay_dates, available_days, promotion):
        final_array = []
        promotion_start = promotion.period_from
        promotion_end = promotion.period_to
        valid_on_premium_dates = promotion.available_in_premium_dates
        valid_on_stop_sell_dates = promotion.available_in_stop_sell_dates

        for key, date in stay_dates.items():
            if date['date'].day not in available_days:
                continue

            if (not date['date'].is_stop_sell() or valid_on_stop_sell_dates) and \
                    (not date['date'].is_premium_date() or valid_on_premium_dates) and \
                    (date['date'].internal_date >= promotion_start) and \
                    (date['date'].internal_date <= promotion_end):
                final_array.append(date['date'].id)

        return final_array

    def _apply_stay_discount(self, benefits_data, benefit, promo_exceptions):
        calculate_discount = benefit.value_type.slug != 'net'

        if not calculate_discount:
            day_discount = round(Decimal(benefit.value) / benefits_data['daysCount'], 4)

        for key, date_object in benefits_data['dates'].items():
            if not self._promotion_applies_to_date(date_object, promo_exceptions, benefits_data['datesKeys']):
                continue

            if calculate_discount:
                day_discount = round(Decimal(benefits_data['stayDates'][key]['priceNetRemaining']) * (Decimal(benefit.value) / 100), 4)

            self._apply_price_discount_to_date(benefits_data, key, day_discount)

    def _apply_night_discount(self, benefits_data, benefit, promo_exceptions):
        calculate_discount = benefit.value_type.slug != 'net'
        each_counter = 1
        limit_counter = 0
        free_night = False
        promo_discount = 0
        day_discount = 0

        if not calculate_discount:
            promo_discount = day_discount = benefit.value
        else:  # Free night case: benefit.value == 100 for 'percent'
            free_night = benefit.value == 100

        for key, date_object in benefits_data['dates'].items():
            if not self._promotion_applies_to_date(date_object, promo_exceptions, benefits_data['dates_keys']):
                continue

            if not benefit.each_value or benefit.each_value == 0 or each_counter % benefit.each_value == 0:
                each_counter = 0
                limit_counter += 1
                benefits_data['stay_dates'][key]['is_free'] = free_night

                if calculate_discount:
                    day_discount = benefits_data['stay_dates'][key]['price_net_remaining'] * (benefit.value / 100)

                if benefits_data['stay_dates'][key]['price_net_remaining'] >= day_discount:
                    self._apply_price_discount_to_date(benefits_data, key, day_discount)
                else:
                    # Room net is less than the discount
                    to_apply = benefits_data['stay_dates'][key]['price_net_remaining']
                    self._apply_price_discount_to_date(benefits_data, key, to_apply)
                    if benefit.cumulative and not calculate_discount:
                        promo_discount -= to_apply
                        if promo_discount <= 0:
                            break

                if (benefit.has_limit and limit_counter >= benefit.limit_value) or not benefit.cumulative:
                    break

            each_counter += 1

    def _promotion_applies_to_date(self, the_date, exceptions, date_keys):
        if the_date.id not in date_keys:
            return False

        if not exceptions:
            return True

        for exception in exceptions:
            if exception.period_from <= the_date <= exception.period_to:
                return False

        return True

    def _apply_service_discount(self, benefits_data, benefit, promo_exceptions):
        calculate_discount = benefit.value_type.slug != 'net'
        each_counter = 1
        limit_counter = 0
        promo_discount = 0
        day_discount = 0

        if not calculate_discount:
            promo_discount = day_discount = benefit.value

        for key, date_object in benefits_data['dates'].items():
            if not self._promotion_applies_to_date(date_object, promo_exceptions, benefits_data['dates_keys']):
                continue

            if not benefit.each_value or benefit.each_value == 0 or each_counter % benefit.each_value == 0:
                each_counter = 0
                limit_counter += 1

                if calculate_discount:
                    day_discount = benefits_data['stay_dates'][key]['mandatory_services_remaining'] * (benefit.value / 100)

                if benefits_data['stay_dates'][key]['mandatory_services_remaining'] >= day_discount:
                    self._apply_service_discount_to_date(benefits_data, key, day_discount)
                else:
                    # Room net is less than the discount
                    to_apply = benefits_data['stay_dates'][key]['mandatory_services_remaining']
                    self._apply_service_discount_to_date(benefits_data, key, to_apply)
                    if benefit.cumulative and not calculate_discount:
                        promo_discount -= to_apply
                        if promo_discount <= 0:
                            break

                if (benefit.has_limit and limit_counter >= benefit.limit_value) or not benefit.cumulative:
                    break

            each_counter += 1

    def _apply_service_discount(self, benefits_data, benefit, promo_exceptions):
        calculate_discount = benefit.value_type.slug != 'net'
        each_counter = 1
        limit_counter = 0
        promo_discount = 0
        day_discount = 0

        if not calculate_discount:
            promo_discount = day_discount = benefit.value

        for key, date_object in benefits_data['dates'].items():

            if not self._promotion_applies_to_date(date_object, promo_exceptions, benefits_data['datesKeys']):
                continue

            if benefit.each_value is None or benefit.each_value == 0 or each_counter % benefit.each_value == 0:

                each_counter = 0
                limit_counter += 1

                if calculate_discount:
                    day_discount = benefits_data['stayDates'][key]['mandatoryServicesRemaining'] * (benefit.value / 100)

                if benefits_data['stayDates'][key]['mandatoryServicesRemaining'] >= day_discount:
                    self._apply_service_discount_to_date(benefits_data, key, day_discount)
                else:
                    # If room net is < than the discount then, calculate how much we apply.
                    to_apply = benefits_data['stayDates'][key]['mandatoryServicesRemaining']
                    self._apply_service_discount_to_date(benefits_data, key, to_apply)

                    if benefit.cumulative and not calculate_discount:
                        promo_discount -= to_apply
                        if promo_discount <= 0:
                            break

                if (benefit.has_limit and limit_counter >= benefit.limit_value) or not benefit.cumulative:
                    break

            each_counter += 1

    def _apply_service_discount_to_date(self, benefits_data, key, discount):
        self._apply_discount_to_date(benefits_data, key, discount, 'mandatoryServicesRemaining')

    def _apply_price_discount_to_date(self, benefits_data, key, discount):
        self._apply_discount_to_date(benefits_data, key, discount, 'priceNetRemaining')

    def _apply_discount_to_date(self, benefits_data, key, discount, discount_type):
        benefits_data['anyDiscountApplied'] = True
        room_net_with_discount = benefits_data['stayDates'][key][discount_type] - discount

        if room_net_with_discount <= 0:
            benefits_data['stayDates'][key][discount_type] = round(Decimal(0), 2)
        else:
            benefits_data['stayDates'][key][discount_type] = room_net_with_discount

        benefits_data['stayDates'][key]['isPromotion'] = True
        benefits_data['stayDates'][key]['dateWithDiscount'] = True
        benefits_data['stayDates'][key]['promotionCode'] = benefits_data['promoCode']
