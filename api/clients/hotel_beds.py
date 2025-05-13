import json

import requests
import hashlib
import time
from db import db, Search, Hotel
from util import create_range


def save_to_disk(payload):
    with open('data.json', 'w') as f:
        json.dump(payload, f, indent=4)

class HotelBeds(object):

    @staticmethod
    def find_hotels_for_search(search: Search):
        
        hotel_beds = HotelBeds(search)
        result = hotel_beds._find_hotels()
        return result

    def __init__(self, search):
        self._url = "https://api.test.hotelbeds.com/hotel-api/1.0/hotels"
        self._secret = '4ceb50f312'
        self._api_key = '8a50c8a6a7d48103f13121a56117f9da'
        self._search = search
        self._days = create_range(self._search.check_in.date(), self._search.check_out.date())
        self._user = self._search.user
        self._locale = 'en'
        self._use_disk_response = False
        self._save_disk_response = True
        # self._use_disk_response = True
        # self._save_disk_response = False

    def _find_hotels(self):
        self._get_hotels()
        payload = self._build_payload()
        response = self._send_request(payload)
        zohartours = self._build_response(response) 
        return zohartours

    def _get_hotels(self):
        query = db.session.query(Hotel).filter_by(enabled=True)
        query = query.filter(Hotel.hotelbeds_id.isnot(None))
        
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

        self._hotels = {h.hotelbeds_id: h for h in query.all()}

    def _generate_hash(self):
        # Obtener el timestamp actual en segundos
        timestamp = str(int(time.time()))
        
        # Concatenar apiKey, secret y el timestamp
        data = f"{self._api_key}{self._secret}{timestamp}"
        
        # Generar el hash SHA-256
        hash_object = hashlib.sha256(data.encode('utf-8'))
        hash_hex = hash_object.hexdigest()
        
        return hash_hex

    def _send_request(self, payload):
        if self._use_disk_response:
            with open('data.json', 'r') as f:
                data = json.load(f)
                return data

        """
        This function receives a payload to send to the url of hotelbeds.
        If successfull, it returns the response's payload
        If the status code returned by HB is NOT 200 (success), then prints the error and returns None 
        """
        try:
            headers = {
                "Content-Type": "application/json",
                'Api-key': self._api_key,
                'X-Signature': self._generate_hash(),
                'Accept': 'application/json',
                'Accept-Encoding': 'gzip',
            }

            response = requests.post(self._url, json=payload, headers=headers)
            
            # Verificar el estado de la respuesta
            if response.status_code == 200:
                return response.json()
            
            print(f"Error {response.status_code}: {response.text}")
        except requests.exceptions.RequestException as e:
            print("Error al realizar la solicitud:", e)
        
        return None

    def _build_payload(self):

        return {
            "stay": { 
                "checkIn": self._search.check_in.strftime("%Y-%m-%d"),
                "checkOut": self._search.check_out.strftime("%Y-%m-%d")
            },
            "occupancies": [{
                "rooms": 1,
                "adults": self._search.room_1_adults,
                "children": self._search.room_1_children
            }],
            "hotels": {
                "hotel": list(self._hotels.keys())
            }
        }

    def _build_response(self, response_hotel_beds):
        if self._save_disk_response:
            save_to_disk(response_hotel_beds)

        if response_hotel_beds is None:
            print("No response from hotelbeds")
            return []
        if 'hotels' not in response_hotel_beds:
            print("Invalid response from hotelbeds")
            return []

        hotels_hotelbeds = response_hotel_beds['hotels']
        hotels = self._build_zohar_hotels(hotels_hotelbeds)
        return hotels

    def _get_cancellation_date(self, cancellable):
        if not cancellable:
            return self._check_in + " 00:00:00"

        cancellable = cancellable[0]
        return datetime.fromisoformat(cancellable["from"]).strftime("%Y-%m-%d %H:%M:%S")

    def _build_stay_dates(self, rate, dates_entities):
        return [
            self._build_single_stay_date(idx+1, rate, date_entity)
            for idx, date_entity in enumerate(dates_entities)
        ]

    def _build_single_stay_date(self, rate, date_id, the_date):
        the_date.id = date_id
        the_date.minimum_stay = 1
        the_date.maximum_stay = 999
        the_date.date_stock = 0
        the_date.date_cut_off = the_date

        net = float(rate.get("net"))
        return {
            "id": the_date.id,
            "date": the_date,
            "day": the_date.date.day,
            "nightTax":  round(0, 2),
            "isBlackedOut": False,
            "isPremium": False,
            "onRequest": True,
            "isPromotion": False,
            # Calculated values
            "totalNet": round(net, 2),
            "totalWithServicesNet": round(net, 2),
            "price": round(net, 2),
            "tax": round(0, 2),
            "commission": round(0, 2),
            "total": round(net, 2),
            "dateWithDiscount": False,
            "isObject": False,
            "isFree": False,
            "totalWithServices": round(net, 2),
            "totalWithServicesAndCommission": round(net, 2),
            "totalCost": round(net, 2),
        }

    def _build_zohar_single_room(self, room, currency):
        name = room.get("name")

        for rate in room.get("rates", []):
            room_is_cancelable = len(rate.get("cancellationPolicies")) > 0
            children = rate.get("children", 0)
            adults = rate.get("adults", 0)
            taxes = float(0)
            net = float(rate.get("net"))
            stay_dates = self._build_stay_dates(rate, self._days)
            return [
                {
                    "id": rate.get("code"),
                    "name": name,
                    "description": name,
                    "adults": adults,
                    "children": children,
                    "capacity": adults + children,
                    "calendar": make_calendar(stay_dates, self._search.nights, use_net_values=True),
                    "cancellableUntil": self._get_cancellation_date(room_is_cancelable),
                    "commission": float(0),  # de guillermo
                    "currency": currency,
                    "isBlackedOut": False,
                    "isCancellable": room_is_cancelable,
                    "isPromotion": False,
                    "onRequest": True,
                    "mandatoryServices": [],
                    "mandatoryServicesTotal": float(0),
                    "price": net,
                    "taxes": taxes,
                    "total": net + taxes,
                    "totalWithMandatoryServices": net + taxes + 0.00,
                    "stayDates": stay_dates,
                }
            ]

    def _build_zohar_rooms(self, rooms, currency):
        return [self._build_zohar_single_room(r, currency) for r in rooms]

    def _build_zohar_single_hotel(self, hotel):

        zohar_hotel = self._hotels[hotel.get("code")]
        return {
            "is_beds_hotel": True,
            "cancellableUntil": "NOT SET",  # De guillermo
            "cancellationCutOff": 0,
            "adults": hotel.get("rooms")[0].get("rates")[0].get("adults"),
            "children": hotel.get("rooms")[0].get("rates")[0].get("children"),
            "coords": hotel.coords,
            "hasPromotion": False,
            "id": zohar_hotel.id,
            "isBlackedOut": False,
            "lowPrice": float(hotel.get("minRate", 0)),
            "mandatoryServicesPrice": float(0),
            "name": zohar_hotel.name,
            "address": zohar_hotel.address,
            "subtitle": zohar_hotel.subtitle,
            "stars": zohar_hotel.stars,
            "city": zohar_hotel.city,
            "state": zohar_hotel.state,
            "checkIn": zohar_hotel.check_in,
            "checkOut": zohar_hotel.check_out,
            "childAge": zohar_hotel.child_age,
            "currency": zohar_hotel.currency.symbol,
            "rooms": self._build_zohar_rooms(hotel.get("rooms", []), zohar_hotel.currency.symbol),
        }

    def _build_zohar_hotels(self, hotels_node):
        hotels = []
        for hotel in hotels_node['hotels']:
            zohar_hotel = self._build_zohar_single_hotel(hotel)
            hotels.append(zohar_hotel)
        return hotels
