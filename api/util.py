from datetime import datetime, timedelta
from typing import List, Optional
import hashlib
import uuid
from html import unescape


def create_range(date_from, date_to, modify: bool = True) -> List[str]:
    end = date_to
    if modify:
        end += timedelta(days=1)

    date_range = []
    current_date = date_from
    while current_date < end:
        date_range.append(current_date)
        current_date += timedelta(days=1)

    return date_range


def round_number(number: float, mode: str = 'ceil', denominator: int = 4) -> str:
    if mode == 'ceil':
        total = (number * denominator).__ceil__() / denominator
    else:
        total = (number * denominator).__floor__() / denominator
    return f"{total:.2f}"


europe = {
    'Albania': "Europe",
    'Andorra': "Europe",
    'Austria': "Europe",
    'Belarus': "Europe",
    'Belgium': "Europe",
    'Bosnia and Herzegovina': "Europe",
    'Bulgaria': "Europe",
    'Croatia': "Europe",
    'Cyprus': "Europe",
    'Czech Republic': "Europe",
    'Denmark': "Europe",
    'East Germany': "Europe",
    'Estonia': "Europe",
    'Faroe Islands': "Europe",
    'Finland': "Europe",
    'France': "Europe",
    'Germany': "Europe",
    'Gibraltar': "Europe",
    'Greece': "Europe",
    'Guernsey': "Europe",
    'Hungary': "Europe",
    'Iceland': "Europe",
    'Ireland': "Europe",
    'Isle of Man': "Europe",
    'Italy': "Europe",
    'Jersey': "Europe",
    'Latvia': "Europe",
    'Liechtenstein': "Europe",
    'Lithuania': "Europe",
    'Luxembourg': "Europe",
    'Macedonia': "Europe",
    'Malta': "Europe",
    'Metropolitan France': "Europe",
    'Moldova': "Europe",
    'Monaco': "Europe",
    'Montenegro': "Europe",
    'Netherlands': "Europe",
    'Norway': "Europe",
    'Poland': "Europe",
    'Portugal': "Europe",
    'Romania': "Europe",
    'Russia': "Europe",
    'San Marino': "Europe",
    'Serbia': "Europe",
    'Serbia and Montenegro': "Europe",
    'Slovakia': "Europe",
    'Slovenia': "Europe",
    'Spain': "Europe",
    'Svalbard and Jan Mayen': "Europe",
    'Sweden': "Europe",
    'Switzerland': "Europe",
    'Ukraine': "Europe",
    'Union of Soviet Socialist Republics': "Europe",
    'United Kingdom': "Europe",
    'Vatican City': "Europe",
    'Åland Islands': "Europe"
}
america = {
    'Anguilla': "America",
    'Antigua and Barbuda': "America",
    'Argentina': "America",
    'Aruba': "America",
    'Bahamas': "America",
    'Barbados': "America",
    'Belize': "America",
    'Bermuda': "America",
    'Bolivia': "America",
    'Brazil': "America",
    'British Virgin Islands': "America",
    'Canada': "America",
    'Cayman Islands': "America",
    'Chile': "America",
    'Colombia': "America",
    'Costa Rica': "America",
    'Cuba': "America",
    'Dominica': "America",
    'Dominican Republic': "America",
    'Ecuador': "America",
    'El Salvador': "America",
    'Falkland Islands': "America",
    'French Guiana': "America",
    'Greenland': "America",
    'Grenada': "America",
    'Guadeloupe': "America",
    'Guatemala': "America",
    'Guyana': "America",
    'Haiti': "America",
    'Honduras': "America",
    'Jamaica': "America",
    'Martinique': "America",
    'Mexico': "America",
    'México': "America",
    'Montserrat': "America",
    'Netherlands Antilles': "America",
    'Nicaragua': "America",
    'Panama': "America",
    'Paraguay': "America",
    'Peru': "America",
    'Perú': "America",
    'Puerto Rico': "America",
    'Saint Barthélemy': "America",
    'Saint Kitts and Nevis': "America",
    'Saint Lucia': "America",
    'Saint Martin': "America",
    'Saint Pierre and Miquelon': "America",
    'Saint Vincent and the Grenadines': "America",
    'Suriname': "America",
    'Trinidad and Tobago': "America",
    'Turks and Caicos Islands': "America",
    'U.S. Virgin Islands': "America",
    'United States': "America",
    'Uruguay': "America",
    'Venezuela': "America"}


def get_continent(country: str) -> str:
    try:
        return europe[country]
    except KeyError:
        pass
    try:
        return america[country]
    except KeyError:
        pass
    return "Unknown"


def make_code(val: str = '', concat: str = '', mode: str = 'str_pad', complete: str = '0', length: int = 8) -> str:
    # if mode != 'str_pad':
    return hashlib.md5(str(uuid.uuid4()).encode()).hexdigest()[:length]
    # if concat:
    #     return concat + val.rjust(length, complete)
    # return val.rjust(length, complete)


def create_id(concat: Optional[str], val: str, complete: str = '0', length: int = 7) -> str:
    if concat:
        return concat + val.rjust(length, complete)
    return val.rjust(length, complete)


def guid() -> str:
    return str(uuid.uuid4())

occupancy_map = {
    1: "SINGLE", 2: "DOUBLE", 3: "TRIPLE", 4: "QUADRUPLE",
    5: "QUINTUPLE", 6: "SEXTUPLE", 7: "SEPTUPLE", 8: "OCTUPLE",
    9: "NONUPLE", 10: "DECUPLE", 11: "UNDECUPLE", 12: "DUODECUPLE",
    13: "TREDECUPLE"
}
def get_occupancy(adults: int) -> str:
    try:
        return occupancy_map[adults]
    except KeyError:
        return "UNKNOWN"


def make_calendar(dates, nights, use_net_values):
    table = '''<table class="table table-condensed ResultHotelRoom-priceTableDetails">
    <thead>
        <th>Mon</th>
        <th>Tue</th>
        <th>Wed</th>
        <th>Thu</th>
        <th>Fri</th>
        <th>Sat</th>
        <th>Sun</th>
    </thead>
    <tbody>
'''

    first_row = True
    if dates:
        last_date_id = dates[-1]['date'].id

        for date in dates:
            day = date['day']

            if day == 'Mon':
                table += "<tr>"

            if first_row:
                table += get_calendar_row_prefix(day)
                first_row = False

            table += f"<td>{print_price(date, nights, use_net_values)}</td>"

            if date['date'].id == last_date_id:
                table += get_calendar_row_suffix(day)

            if day == 'Sun':
                table += "</tr>"

    table += "</tbody></table>"
    return table


prefixes = {
    'Tue': "<td></td>",
    'Wed': "<td></td><td></td>",
    'Thu': "<td></td><td></td><td></td>",
    'Fri': "<td></td><td></td><td></td><td></td>",
    'Sat': "<td></td><td></td><td></td><td></td><td></td>",
    'Sun': "<td></td><td></td><td></td><td></td><td></td><td></td>"
}

def get_calendar_row_prefix(day):
    try:
        return prefixes[day]
    except KeyError:
        return ""


suffixes = {
    'Mon': "<td></td><td></td><td></td><td></td><td></td><td></td></tr>",
    'Tue': "<td></td><td></td><td></td><td></td><td></td></tr>",
    'Wed': "<td></td><td></td><td></td><td></td></tr>",
    'Thu': "<td></td><td></td><td></td></tr>",
    'Fri': "<td></td><td></td></tr>",
    'Sat': "<td></td></tr>"
}

def get_calendar_row_suffix(day):
    try:
        return suffixes[day]
    except KeyError:
        return ""


def days_until_check_in(the_date):
    return (the_date.date() - datetime.today().date()).days


def print_price(date, nights, use_net_values):
    message = ""

    minimum_stay = date["date"].minimum_stay
    maximum_stay = date["date"].maximum_stay
    date_stock = date["date"].stock
    date_cut_off = date["date"].cut_off
    date_date = date["date"].get_date("%d %b %Y")  # Format as "d M Y"
    is_on_request = date["onRequest"]
    is_free = date["isFree"]
    is_premium = date["isPremium"]

    price = date["totalCost"] if use_net_values else date["totalWithServicesAndCommission"]

    days_until_checkin = days_until_check_in(date["date"].date)  # Assuming days_until_check_in is implemented

    if is_free:
        message += "Night off\n"

    if days_until_checkin < date_cut_off:
        message += f"Cut-off required ({date_cut_off} nights)\n"

    if nights < minimum_stay:
        message += f"Minimum stay required ({minimum_stay} nights)\n"

    if nights > maximum_stay:
        message += f"Maximum stay required ({maximum_stay} nights)\n"

    if date_stock < 1:
        message += "On Request\n"

    if price <= 0:
        price = "Free" if is_free else "On Request"
    else:
        price = f"$ {price:,.2f}"

    if is_on_request:
        code = f"<span class='label label-info'>{date_date}</span><br><span class='label label-warning' data-toggle='tooltip' data-placement='bottom' title='On Request: {message}'> {price}</span>"
    elif is_free:
        code = f"<span class='label label-info'>{date_date}</span><br><span class='label label-success' data-toggle='tooltip' data-placement='bottom' title='Free: {message}'> {price}</span>"
    elif is_premium:
        code = f"<span class='label label-info'>{date_date}</span><br><span class='label label-success' data-toggle='tooltip' data-placement='bottom' title='Available, Premium Date'> {price}</span>"
    else:
        code = f"<span class='label label-info'>{date_date}</span><br><span class='label label-success tooltip-success' data-toggle='tooltip' data-placement='bottom' title='Available'> {price}</span>"

    return unescape(code)
