import traceback
from datetime import datetime
from dateutil import relativedelta
from concurrent.futures import ThreadPoolExecutor
from flask import Flask, request, jsonify

from db import db, Search
from clients import ZoharTours, HotelBeds
from util import make_code


app = Flask(__name__)
app.config["SQLALCHEMY_DATABASE_URI"] = "sqlite:///zohartours.db"
db.init_app(app)


def create_dummy_search_record():
    city = ""
    state = ""
    country = "United States"
    continent = "America"
    region = "United States"
    rooms = 1
    check_in = datetime.today() + relativedelta.relativedelta(weeks=1)
    check_out = check_in + relativedelta.relativedelta(days=4)
    children = 0
    adults = 2
    children_ages = ["0", "0", "0", "0", "0", "0"]
    nights = (check_out - check_in).days
    user_id = 2  # Replace with the actual user ID you want to search for

    new_search = Search(
        user_id=user_id,
        hotel="",
        city=city,
        state=state,
        country=country,
        continent=continent,
        region=region,
        rooms=rooms,
        check_in=check_in,
        check_out=check_out,
        room_1_adults=adults,
        room_1_children=children,
        room_1_children_age=children_ages,
        nights=nights,
        search_code=make_code()
    )

    db.session.add(new_search)
    db.session.commit()
    return new_search


use_parallel = False
def hotels_search_by_id(search_id):
    search = db.session.query(Search).filter(Search.id==search_id).first()
    if use_parallel:
        with (ThreadPoolExecutor() as executor):
            future1 = executor.submit(ZoharTours.find_hotels_for_search, search)
            future2 = executor.submit(HotelBeds.find_hotels_for_search, search)
            hotels_zohar = future1.result()
            hotels_beds = future2.result()
    else:
        hotels_zohar = ZoharTours.find_hotels_for_search(search)
        hotels_beds = HotelBeds.find_hotels_for_search(search)

    return hotels_zohar + hotels_beds

@app.route("/hotels", methods=["GET", "POST"])
def search_hotels_both():
    search_id = request.args.get("search_id")
    if not search_id:
        search = create_dummy_search_record()
        search_id = search.id
        # return jsonify({"error": "Missing search_id parameter"}), 400

    try:
        hotels = hotels_search_by_id(search_id)
        return jsonify(hotels)
    except Exception as e:
        error = traceback.format_exc()
        print(error)
        return jsonify({"error": error}), 500


if __name__ == "__main__":
    app.run(debug=True, port=5001)
