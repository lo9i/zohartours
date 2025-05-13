from datetime import datetime, time
from decimal import Decimal

from flask_sqlalchemy import SQLAlchemy
from sqlalchemy.orm import Mapped, mapped_column, relationship, sessionmaker
from sqlalchemy import String, Text, Boolean, DateTime, ForeignKey, Integer, JSON, func, DECIMAL, select, Numeric, \
    Column, Table
from typing import List, Optional

db = SQLAlchemy()


def create_session():
    return sessionmaker(bind=db.engine)


hotel_amenities_association = Table(
    "hotel_amenities",
    db.metadata,
    Column("hotel_id", ForeignKey("hotel.id"), primary_key=True),
    Column("amenity_id", ForeignKey("amenity.id"), primary_key=True),
)


class Hotel(db.Model):
    __tablename__ = 'hotel'
    id: Mapped[int] = mapped_column(primary_key=True)
    name: Mapped[str] = mapped_column(unique=True)
    en_description: Mapped[str]
    es_description: Mapped[str]
    address: Mapped[str]
    website: Mapped[str]
    city: Mapped[str]
    state: Mapped[str]
    country: Mapped[str]
    zip_code: Mapped[str]
    check_in: Mapped[time]
    check_out: Mapped[time]
    reservation_email: Mapped[str]
    cancellation_email: Mapped[str]
    coords: Mapped[str]
    enabled: Mapped[int]
    slug: Mapped[str]
    child_age: Mapped[int]
    stars: Mapped[float]
    created_at: Mapped[datetime]
    updated_at: Mapped[datetime]
    subtitle: Mapped[str]
    currency_id: Mapped[int] = mapped_column(ForeignKey("hotel_currency.id"))
    currency = relationship("HotelCurrency", back_populates="hotels", lazy="joined")
    continent: Mapped[str]
    city_id: Mapped[int] = mapped_column(ForeignKey("city.id"))
    phone: Mapped[str]
    region: Mapped[str]
    vip: Mapped[int]
    video_url: Mapped[str]
    es_voucher_note: Mapped[str]
    en_voucher_note: Mapped[str]
    hotelbeds_id: Mapped[str]

    services = relationship("HotelService", back_populates="hotel", lazy="joined")
    # promotions = relationship("HotelPromotion", back_populates="hotel", lazy="joined")
    rooms = relationship("Room", back_populates="hotel", lazy="joined")
    amenities: Mapped[List["Amenity"]] = relationship(
        secondary=hotel_amenities_association,
        back_populates="hotels",
        lazy = "joined"
    )

    def get_mandatory_services(self):
        return db.session.execute(
            select(HotelService)
                .join(ServiceType)
                .filter(ServiceType.slug == 'mandatory',
                        HotelService.hotel_id == self.id,
                )
        ).scalars().all()

    def get_mandatory_services_total(self, pax=2, nights=1):
        mandatory_services = db.session.execute(
            select(HotelService)
                .join(ServiceType)
                .filter(ServiceType.slug == 'mandatory',
                        HotelService.hotel_id == self.id,)
            ).scalars().all()

        total = 0
        for service in mandatory_services:
            total += service.get_sale_price(pax, nights)

        return total

    def get_real_adults(hotel, adults, children_ages) -> int:
        free_children_age = hotel.get_child_age()

        child_real_adults = 0
        for child_age in children_ages:
            if child_age > free_children_age:
                child_real_adults += 1

        return adults + child_real_adults

    def as_dict(self):
        return {
            'id': self.id,
            'name': self.name,
            'city': self.city,
            'state': self.state,
            'country': self.country,
            'stars': self.stars,
            'created_at': self.created_at,
            'updated_at': self.updated_at,
            'enabled': self.enabled,
            'coordinates': self.coords,
            'rooms': [room.as_dict() for room in self.rooms] if hasattr(self, 'rooms') else []
        }


class ServiceType(db.Model):
    __tablename__ = "service_type"

    id: Mapped[int] = mapped_column(Integer, primary_key=True, autoincrement=True)
    name: Mapped[str] = mapped_column(String(255), nullable=False)
    slug: Mapped[str] = mapped_column(String(255), unique=True, nullable=False)
    created_at: Mapped[datetime] = mapped_column(DateTime, default=func.now())
    updated_at: Mapped[datetime] = mapped_column(DateTime, default=func.now(), onupdate=func.now())

    # Relationships
    hotel_services: Mapped[list["HotelService"]] = relationship("HotelService", back_populates="service_type")
    room_services: Mapped[list["RoomService"]] = relationship("RoomService", back_populates="service_type")

    def __repr__(self):
        return f"<ServiceType(name={self.name}, slug={self.slug})>"


class Room(db.Model):
    __tablename__ = "room"
    id: Mapped[int] = mapped_column(primary_key=True)
    name: Mapped[str]
    en_description: Mapped[str]
    es_description: Mapped[str]
    capacity: Mapped[int]
    hotel_id: Mapped[int] = mapped_column(ForeignKey("hotel.id"))

    hotel = relationship("Hotel", back_populates="rooms")
    services = relationship("RoomService", back_populates="room")

    rates = relationship("Rate")
    dates = relationship("Date")
    promotions = relationship("Promotion")

    def get_description(self, locale):
        return self.es_description if locale == "es" else self.en_description

    def get_combinable_promotions(self):
        return db.session.query(Promotion).filter(Promotion.enabled == True, Promotion.combinable == True).all()

    def get_non_combinable_promotions(self):
        return db.session.query(Promotion).filter(Promotion.enabled == True, Promotion.combinable == False).all()

    def get_mandatory_services(self):
        return db.session.execute(
            select(RoomService)
                .join(ServiceType)
                .where(ServiceType.slug == 'mandatory')
        ).scalars().all()

    def get_mandatory_services_total(self, pax=2, nights=1):
        mandatory_services = db.session.execute(
            select(RoomService)
                .join(ServiceType)
                .where(ServiceType.slug == 'mandatory')
        ).scalars().all()

        total = 0
        for service in mandatory_services:
            total += service.get_sale_price(pax, nights)

        return total

    def as_dict(self):
        return {
            'id': self.id,
            'name': self.name,
            'en_description': self.en_description,
            'es_description': self.es_description,
            'capacity': self.capacity,
            # 'dates': [date.as_dict() for date in self.dates],
            # 'rates': [rate.as_dict() for rate in self.rates],
        }


class HotelCurrency(db.Model):
    __tablename__ = 'hotel_currency'

    # Primary key with auto-increment
    id: Mapped[int] = mapped_column(Integer, primary_key=True, autoincrement=True)

    # Name field
    name: Mapped[str] = mapped_column(String(255), nullable=False)

    # Symbol field
    symbol: Mapped[str] = mapped_column(String(255), nullable=False)

    # Short name field
    short_name: Mapped[str] = mapped_column("short_name", String(255), nullable=False)

    # Slug field
    slug: Mapped[str] = mapped_column(String(255), unique=True, nullable=False)

    # One-to-many relationship with Hotel
    hotels: Mapped[list['Hotel']] = relationship('Hotel', back_populates='currency')

    # Created At timestamp
    created_at: Mapped[DateTime] = mapped_column(DateTime, default=func.now(), nullable=False)

    # Updated At timestamp
    updated_at: Mapped[DateTime] = mapped_column(DateTime, default=func.now(), onupdate=func.now(), nullable=False)

    def __repr__(self):
        return f"{self.name} ({self.short_name})"

    def __str__(self):
        return f"{self.name} ({self.short_name})"


class HotelService(db.Model):
    __tablename__ = "hotel_service"
    id: Mapped[int] = mapped_column(primary_key=True)
    hotel_id: Mapped[int] = mapped_column(ForeignKey("hotel.id"))
    hotel = relationship("Hotel", back_populates="services")
    sale_price: Mapped[Decimal] = mapped_column(Numeric(15, 2), nullable=True)
    service_type_id: Mapped[int] = mapped_column(Integer, ForeignKey("service_type.id"))
    service_type: Mapped["ServiceType"] = relationship("ServiceType", back_populates="hotel_services")
    service_pay_type_id: Mapped[int] = mapped_column(Integer, ForeignKey("service_pay_type.id"))
    service_pay_type: Mapped["ServicePayType"] = relationship("ServicePayType", back_populates="hotel_services")
    profit_type_id: Mapped[int] = mapped_column(Integer, ForeignKey("profit_type.id"))
    profit_type: Mapped["ProfitType"] = relationship("ProfitType", back_populates="hotel_services")
    benefits: Mapped[list["PromotionBenefit"]] = relationship("PromotionBenefit", back_populates="hotel_service", cascade="all, delete-orphan")
   
    def get_sale_price(self, paxes: int = 1, nights: int = 1) -> Optional[float]:
        if not self.service_pay_type or not self.service_pay_type.slug:
            return None  # Handle missing service pay type

        type_slug = self.service_pay_type.slug
        if type_slug == "unique-per-room":
            return self.sale_price
        if type_slug == "unique-per-person":
            return self.sale_price * paxes
        if type_slug == "per-night-per-room":
            return self.sale_price * nights
        if type_slug == "per-night-per-person":
            return self.sale_price * paxes * nights

        return None  # Handle missing service pay type


class HotelPromotion(db.Model):
    __tablename__ = "hotel_promotion"
    id: Mapped[int] = mapped_column(primary_key=True)
    # hotel_id: Mapped[int] = mapped_column(ForeignKey("hotel.id"))
    # hotel = relationship("Hotel", back_populates="promotions")


class Amenity(db.Model):
    __tablename__ = "amenity"
    id: Mapped[int] = mapped_column(primary_key=True)
    name: Mapped[str] = mapped_column(String(255), nullable=False)
    description: Mapped[str | None] = mapped_column(String, nullable=True)
    hotels: Mapped[List["Hotel"]] = relationship(
        secondary=hotel_amenities_association,
        back_populates="amenities",
        lazy="joined"
    )


class RoomService(db.Model):
    __tablename__ = "room_service"

    id: Mapped[int] = mapped_column(Integer, primary_key=True, autoincrement=True)
    name: Mapped[str] = mapped_column(String(255), nullable=False)
    description: Mapped[str | None] = mapped_column(String, nullable=True)

    price: Mapped[Decimal] = mapped_column(Numeric(15, 2), nullable=False)
    tax: Mapped[Decimal] = mapped_column(Numeric(15, 2), nullable=False)
    sale_price: Mapped[Decimal] = mapped_column(Numeric(15, 2), nullable=True)
    net_cost: Mapped[Decimal] = mapped_column(Numeric(15, 2), nullable=True)
    net_profit: Mapped[Decimal] = mapped_column(Numeric(15, 2), nullable=True)
    net_tax: Mapped[Decimal] = mapped_column(Numeric(15, 2), nullable=True)
    profit: Mapped[Decimal] = mapped_column(Numeric(15, 2), nullable=False)

    enabled: Mapped[bool] = mapped_column(Boolean, default=True, nullable=False)
    slug: Mapped[str] = mapped_column(String(255), unique=True, nullable=False)

    created_at: Mapped[datetime] = mapped_column(DateTime, default=func.now())
    updated_at: Mapped[datetime] = mapped_column(DateTime, default=func.now(), onupdate=func.now())

    # Relationships
    profit_type_id: Mapped[int] = mapped_column(Integer, ForeignKey("profit_type.id"))
    profit_type: Mapped["ProfitType"] = relationship("ProfitType", back_populates="room_services")

    room_id: Mapped[int] = mapped_column(Integer, ForeignKey("room.id"))
    room: Mapped["Room"] = relationship("Room", back_populates="services")

    service_type_id: Mapped[int] = mapped_column(Integer, ForeignKey("service_type.id"))
    service_type: Mapped["ServiceType"] = relationship("ServiceType", back_populates="room_services")

    service_pay_type_id: Mapped[int] = mapped_column(Integer, ForeignKey("service_pay_type.id"))
    service_pay_type: Mapped["ServicePayType"] = relationship("ServicePayType", back_populates="room_services")

    benefits: Mapped[list["PromotionBenefit"]] = relationship("PromotionBenefit", back_populates="room_service", cascade="all, delete-orphan")

    def get_sale_price(self, paxes: int = 1, nights: int = 1) -> Optional[float]:
        if not self.service_pay_type or not self.service_pay_type.slug:
            return None  # Handle missing service pay type

        type_slug = self.service_pay_type.slug
        if type_slug == "unique-per-room":
            return self.sale_price
        if type_slug == "unique-per-person":
            return self.sale_price * paxes
        if type_slug == "per-night-per-room":
            return self.sale_price * nights
        if type_slug == "per-night-per-person":
            return self.sale_price * paxes * nights

        return None  # Handle missing service pay type

    def __repr__(self):
        return f"<RoomService(name={self.name}, price={self.price}, enabled={self.enabled})>"


class ServicePayType(db.Model):
    __tablename__ = "service_pay_type"

    id: Mapped[int] = mapped_column(Integer, primary_key=True, autoincrement=True)
    name: Mapped[str] = mapped_column(String(255), nullable=False)
    slug: Mapped[str] = mapped_column(String(255), unique=True, nullable=False)

    created_at: Mapped[datetime] = mapped_column(DateTime, default=func.now())
    updated_at: Mapped[datetime] = mapped_column(DateTime, default=func.now(), onupdate=func.now())

    hotel_services: Mapped[list["HotelService"]] = relationship("HotelService", back_populates="service_pay_type")
    room_services: Mapped[list["RoomService"]] = relationship("RoomService", back_populates="service_pay_type")


class ProfitType(db.Model):
    __tablename__ = "profit_type"

    id: Mapped[int] = mapped_column(Integer, primary_key=True, autoincrement=True)
    name: Mapped[str] = mapped_column(String(255), nullable=False)
    slug: Mapped[str] = mapped_column(String(255), unique=True, nullable=False)

    created_at: Mapped[datetime] = mapped_column(DateTime, default=func.now())
    updated_at: Mapped[datetime] = mapped_column(DateTime, default=func.now(), onupdate=func.now())

    rates: Mapped[list["Rate"]] = relationship("Rate", back_populates="profit_type")
    hotel_services: Mapped[list["HotelService"]] = relationship("HotelService", back_populates="profit_type")
    room_services: Mapped[list["RoomService"]] = relationship("RoomService", back_populates="profit_type")


class Rate(db.Model):
    __tablename__ = "rate"
    id: Mapped[int] = mapped_column(primary_key=True)
    name: Mapped[str]
    room_id: Mapped[int] = mapped_column(ForeignKey("room.id"))
    room = relationship("Room", back_populates="rates")
    profit_type_id: Mapped[int] = mapped_column(ForeignKey("profit_type.id"))
    profit_type = relationship("ProfitType", back_populates="rates")
    
    tax: Mapped[Decimal] = mapped_column(Numeric(15, 2), nullable=False)
    profit: Mapped[Decimal] = mapped_column(Numeric(15, 2), nullable=True)
    price: Mapped[Decimal] = mapped_column(Numeric(15, 2), nullable=False)
    price_triple: Mapped[Decimal] = mapped_column(Numeric(15, 2), nullable=False)
    price_quadruple: Mapped[Decimal] = mapped_column(Numeric(15, 2), nullable=False)
    price_quintuple: Mapped[Decimal] = mapped_column(Numeric(15, 2), nullable=False)
    price_sextuple: Mapped[Decimal] = mapped_column(Numeric(15, 2), nullable=False)
    price_septuple: Mapped[Decimal] = mapped_column(Numeric(15, 2), nullable=False)
    price_octuple: Mapped[Decimal] = mapped_column(Numeric(15, 2), nullable=False)
    occupancy_tax: Mapped[Decimal] = mapped_column(Numeric(15, 2), nullable=False)

    def as_dict(self):
        return {
            'id': self.id,
            'name': self.name,
            'price': self.price,
            'room_id': self.room_id
        }

    def get_price(self, pax: int = 2) -> float:
        total = 0
        if pax >= 8:
            total += self.price_octuple
        if pax >= 7:
            total += self.price_septuple
        if pax >= 6:
            total += self.price_sextuple
        if pax >= 5:
            total += self.price_quintuple
        if pax >= 4:
            total += self.price_quadruple
        if pax >= 3:
            total += self.price_triple

        total += self.price  # Default case

        return round(total, 2)


class CancellationPolicy(db.Model):
    __tablename__ = 'cancellation_policy'

    id: Mapped[int] = mapped_column(Integer, primary_key=True, autoincrement=True)
    name: Mapped[str] = mapped_column(String(255), nullable=False)
    cut_off: Mapped[int] = mapped_column(Integer, default=7)
    penalty: Mapped[Decimal] = mapped_column(Numeric(15, 2), nullable=False)

    hotel_id: Mapped[int | None] = mapped_column(Integer, ForeignKey('hotel.id'))
    hotel: Mapped[Hotel | None] = relationship("Hotel", backref="policies")

    cancellation_policy_type_id: Mapped[int | None] = mapped_column(Integer, ForeignKey('cancellation_policy_type.id'))
    cancellation_policy_type: Mapped["CancellationPolicyType"] = relationship("CancellationPolicyType", backref="cancellation_policies")

    def __str__(self) -> str:
        return self.name


class CancellationPolicyType(db.Model):
    __tablename__ = 'cancellation_policy_type'

    id: Mapped[int] = mapped_column(Integer, primary_key=True, autoincrement=True)
    name: Mapped[str] = mapped_column(String(255), nullable=False)

    # Generating slug from the 'name' field
    slug: Mapped[str] = mapped_column(String(255), unique=True, nullable=False)

    created_at: Mapped[DateTime] = mapped_column(DateTime, default=datetime.utcnow)
    updated_at: Mapped[DateTime] = mapped_column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)

    # Relationship with CancellationPolicy
    # cancellation_policies: Mapped["CancellationPolicy"] = relationship("CancellationPolicy", backref="cancellation_policy_type")

    def __str__(self) -> str:
        return self.name


class Date(db.Model):
    __tablename__ = 'date'

    id: Mapped[int] = mapped_column(Integer, primary_key=True, autoincrement=True)
    name: Mapped[str | None] = mapped_column(String(255), nullable=True)
    date: Mapped[DateTime | None] = mapped_column(DateTime, nullable=True)
    cut_off: Mapped[int] = mapped_column(Integer, default=3)
    minimum_stay: Mapped[int] = mapped_column(Integer, default=1)
    maximum_stay: Mapped[int] = mapped_column(Integer, default=29)
    stock: Mapped[int] = mapped_column(Integer, default=0)
    enabled: Mapped[bool] = mapped_column(Boolean, default=True)
    premium_date: Mapped[bool] = mapped_column(Boolean, default=False)
    stop_sell: Mapped[bool] = mapped_column(Boolean, default=False)
    black_out: Mapped[bool] = mapped_column(Boolean, default=False)
    daily_rates: Mapped[bool] = mapped_column(Boolean, default=False)
    date_from: Mapped[DateTime | None] = mapped_column(DateTime, nullable=True)
    date_to: Mapped[DateTime | None] = mapped_column(DateTime, nullable=True)
    created_at: Mapped[DateTime] = mapped_column(DateTime, default=datetime.utcnow)
    updated_at: Mapped[DateTime] = mapped_column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)

    room_id: Mapped[int | None] = mapped_column(Integer, ForeignKey('room.id'))

    monday_rate_id: Mapped[int | None] = mapped_column(Integer, ForeignKey('rate.id'))
    tuesday_rate_id: Mapped[int | None] = mapped_column(Integer, ForeignKey('rate.id'))
    wednesday_rate_id: Mapped[int | None] = mapped_column(Integer, ForeignKey('rate.id'))
    thursday_rate_id: Mapped[int | None] = mapped_column(Integer, ForeignKey('rate.id'))
    friday_rate_id: Mapped[int | None] = mapped_column(Integer, ForeignKey('rate.id'))
    saturday_rate_id: Mapped[int | None] = mapped_column(Integer, ForeignKey('rate.id'))
    sunday_rate_id: Mapped[int | None] = mapped_column(Integer, ForeignKey('rate.id'))

    monday_rate: Mapped[Rate | None] = relationship("Rate", foreign_keys=[monday_rate_id])
    tuesday_rate: Mapped[Rate | None] = relationship("Rate", foreign_keys=[tuesday_rate_id])
    wednesday_rate: Mapped[Rate | None] = relationship("Rate", foreign_keys=[wednesday_rate_id])
    thursday_rate: Mapped[Rate | None] = relationship("Rate", foreign_keys=[thursday_rate_id])
    friday_rate: Mapped[Rate | None] = relationship("Rate", foreign_keys=[friday_rate_id])
    saturday_rate: Mapped[Rate | None] = relationship("Rate", foreign_keys=[saturday_rate_id])
    sunday_rate: Mapped[Rate | None] = relationship("Rate", foreign_keys=[sunday_rate_id])

    cancellation_policy_id: Mapped[int | None] = mapped_column(Integer, ForeignKey('cancellation_policy.id'))
    cancellation_policy: Mapped[CancellationPolicy | None] = relationship("CancellationPolicy", backref="dates")

    def __str__(self) -> str:
        return self.date.strftime('%Y-%m-%d %H:%M:%S') if self.date else ''

    def get_date(self, format: str | None = None) -> str:
        if self.date:
            return self.date.strftime(format) if format else self.date.strftime('%Y-%m-%d %H:%M:%S')
        return ''

    def get_internal_date(self) -> DateTime | None:
        return self.date

    def get_sale_price(self, pax: int = 2, nights: int = 1) -> float:
        return self.get_rate().get_sale_price(pax, nights)

    def get_rate(self) -> "Rate":
        if self.date:
            day = self.date.strftime('%A')
            return {
                'Monday': self.monday_rate,
                'Tuesday': self.tuesday_rate,
                'Wednesday': self.wednesday_rate,
                'Thursday': self.thursday_rate,
                'Friday': self.friday_rate,
                'Saturday': self.saturday_rate,
                'Sunday': self.sunday_rate,
            }.get(day, self.sunday_rate)
        return self.sunday_rate

    def get_day(self) -> str:
        return self.date.strftime('%a') if self.date else ''

    def is_stop_sell(self) -> bool:
        return self.stop_sell

    def is_premium_date(self) -> bool:
        return self.premium_date

    def as_dict(self):
        return {
            'id': self.id,
            'date': self.date,
            'room_id': self.room_id
        }


class Promotion(db.Model):
    __tablename__ = "promotion"

    id: Mapped[int] = mapped_column(primary_key=True)
    name: Mapped[str] = mapped_column(String(255), nullable=False)
    description: Mapped[Optional[str]] = mapped_column(Text, nullable=True)
    enabled: Mapped[bool] = mapped_column(Boolean, default=True)

    valid_from: Mapped[Optional[datetime]] = mapped_column(DateTime, nullable=True)
    valid_to: Mapped[Optional[datetime]] = mapped_column(DateTime, nullable=True)
    period_from: Mapped[Optional[datetime]] = mapped_column(DateTime, nullable=True)
    period_to: Mapped[Optional[datetime]] = mapped_column(DateTime, nullable=True)

    combinable: Mapped[bool] = mapped_column(Boolean, default=False)
    non_refundable: Mapped[bool] = mapped_column(Boolean, default=False)

    available_monday: Mapped[bool] = mapped_column(Boolean, default=True)
    available_tuesday: Mapped[bool] = mapped_column(Boolean, default=True)
    available_wednesday: Mapped[bool] = mapped_column(Boolean, default=True)
    available_thursday: Mapped[bool] = mapped_column(Boolean, default=True)
    available_friday: Mapped[bool] = mapped_column(Boolean, default=True)
    available_saturday: Mapped[bool] = mapped_column(Boolean, default=True)
    available_sunday: Mapped[bool] = mapped_column(Boolean, default=True)

    available_in_premium_dates: Mapped[bool] = mapped_column(Boolean, default=False)
    available_in_stop_sell_dates: Mapped[bool] = mapped_column(Boolean, default=False)
    cut_off: Mapped[int] = mapped_column(Integer, default=0)

    created_at: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow)
    updated_at: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)

    room_id: Mapped[Optional[int]] = mapped_column(ForeignKey("room.id"), nullable=True)

    conditions: Mapped[List["PromotionCondition"]] = relationship(
        "PromotionCondition", back_populates="promotion", cascade="all, delete-orphan"
    )
    benefits: Mapped[List["PromotionBenefit"]] = relationship(
        "PromotionBenefit", back_populates="promotion", cascade="all, delete-orphan"
    )
    exceptions: Mapped[List["PromotionExceptionPeriod"]] = relationship(
        "PromotionExceptionPeriod", back_populates="promotion", cascade="all, delete-orphan"
    )

    def get_available_days(self) -> List[str]:
        days = []
        if self.available_monday:
            days.append("Mon")
        if self.available_tuesday:
            days.append("Tue")
        if self.available_wednesday:
            days.append("Wed")
        if self.available_thursday:
            days.append("Thu")
        if self.available_friday:
            days.append("Fri")
        if self.available_saturday:
            days.append("Sat")
        if self.available_sunday:
            days.append("Sun")
        return days

    def get_comparation_conditions(self) -> List:
        return [
            condition for condition in self.conditions
            if condition.expression.group == "comparation"
        ]

    def __repr__(self) -> str:
        return f"<Promotion(id={self.id}, title='{self.title}', discount={self.discount}, is_active={self.is_active})>"


class PromotionBenefit(db.Model):
    __tablename__ = "promotion_benefit"

    id: Mapped[int] = mapped_column(Integer, primary_key=True, autoincrement=True)
    value: Mapped[float] = mapped_column(DECIMAL(15, 2), nullable=False)
    cumulative: Mapped[bool] = mapped_column(Boolean, default=False)
    each_value: Mapped[Optional[int]] = mapped_column(Integer, nullable=True, default=1)
    has_limit: Mapped[bool] = mapped_column(Boolean, default=False)
    limit_value: Mapped[Optional[int]] = mapped_column(Integer, nullable=True, default=1)

    created_at: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow)
    updated_at: Mapped[datetime] = mapped_column(
        DateTime, default=datetime.utcnow, onupdate=datetime.utcnow
    )

    # Foreign key relationships
    promotion_id: Mapped[Optional[int]] = mapped_column(
        Integer, ForeignKey("promotion.id", ondelete="SET NULL"), nullable=True
    )
    promotion: Mapped[Optional["Promotion"]] = relationship(back_populates="benefits")

    type_id: Mapped[Optional[int]] = mapped_column(
        Integer, ForeignKey("promotion_benefit_type.id", ondelete="SET NULL"), nullable=True
    )
    type: Mapped[Optional["PromotionBenefitType"]] = relationship(back_populates="benefits")

    value_type_id: Mapped[Optional[int]] = mapped_column(
        Integer, ForeignKey("promotion_benefit_value_type.id", ondelete="SET NULL"), nullable=True
    )
    value_type: Mapped[Optional["PromotionBenefitValueType"]] = relationship(back_populates="benefits")

    room_service_id: Mapped[Optional[int]] = mapped_column(
        Integer, ForeignKey("room_service.id", ondelete="SET NULL"), nullable=True
    )
    room_service: Mapped[Optional["RoomService"]] = relationship(back_populates="benefits")

    hotel_service_id: Mapped[Optional[int]] = mapped_column(
        Integer, ForeignKey("hotel_service.id", ondelete="SET NULL"), nullable=True
    )
    hotel_service: Mapped[Optional["HotelService"]] = relationship(back_populates="benefits")

    def __repr__(self) -> str:
        return f"<PromotionBenefit(id={self.id}, value={self.value})>"

    def __str__(self) -> str:
        """Equivalent to PHP __toString()"""
        slug = self.value_type.slug if self.value_type else "unknown"
        value_str = f"$ {self.value}" if slug == "net" else f"{self.value} %"

        discount = None
        type_group = self.type.group.lower() if self.type else ""

        if type_group == "discounts":
            discount = self.type
        elif type_group == "hotel-services":
            discount = self.hotel_service
        elif type_group == "room-services":
            discount = self.room_service

        return f"{value_str} off | {discount}" if discount else value_str


class PromotionBenefitType(db.Model):
    __tablename__ = 'promotion_benefit_type'

    # Define columns using mapped_column
    id = mapped_column(Integer, primary_key=True, autoincrement=True)
    name = mapped_column(String(255), nullable=False)
    group = mapped_column(String(255), nullable=False)
    slug = mapped_column(String(255), nullable=False)
    created_at = mapped_column(DateTime, default=func.now())
    updated_at = mapped_column(DateTime, default=func.now(), onupdate=func.now())

    # Define the relationship to PromotionBenefit
    benefits = relationship('PromotionBenefit', back_populates='type')

    def __repr__(self):
        return f"<PromotionBenefitType(name={self.name})>"


class PromotionBenefitValueType(db.Model):
    __tablename__ = 'promotion_benefit_value_type'

    # Define columns using mapped_column
    id = mapped_column(Integer, primary_key=True, autoincrement=True)
    name = mapped_column(String(255), nullable=False)
    slug = mapped_column(String(255), nullable=False)
    created_at = mapped_column(DateTime, default=func.now())
    updated_at = mapped_column(DateTime, default=func.now(), onupdate=func.now())

    # Relationship to PromotionBenefit
    benefits = relationship('PromotionBenefit', back_populates='value_type')

    def __repr__(self):
        return f"<PromotionBenefitValueType(name={self.name})>"


class PromotionCondition(db.Model):
    __tablename__ = "promotion_condition"

    id: Mapped[int] = mapped_column(Integer, primary_key=True, autoincrement=True)
    value: Mapped[int] = mapped_column(Integer, nullable=False)

    created_at: Mapped[datetime] = mapped_column(DateTime, default=func.now(), nullable=False)
    updated_at: Mapped[datetime] = mapped_column(DateTime, default=func.now(), onupdate=func.now(), nullable=False)

    promotion_id: Mapped[Optional[int]] = mapped_column(ForeignKey("promotion.id"), nullable=True)
    conditional_id: Mapped[Optional[int]] = mapped_column(ForeignKey("promotion_condition_conditional.id"),
                                                          nullable=True)
    expression_id: Mapped[Optional[int]] = mapped_column(ForeignKey("promotion_condition_expression.id"), nullable=True)

    promotion: Mapped[Optional["Promotion"]] = relationship(back_populates="conditions")
    conditional: Mapped[Optional["PromotionConditionConditional"]] = relationship(back_populates="conditions")
    expression: Mapped[Optional["PromotionConditionExpression"]] = relationship(back_populates="conditions")

    def __repr__(self) -> str:
        return f"<PromotionCondition(value={self.value})>"

    def __str__(self) -> str:
        slug = self.expression.get_slug() if self.expression else None
        if slug != "for-each":
            return f"{self.conditional} {self.expression} {self.value}"
        else:
            conditional = str(self.conditional).lower() if self.conditional else ""
            return f"Each {self.value} {conditional}"


class PromotionConditionConditional(db.Model):
    __tablename__ = "promotion_condition_conditional"

    id: Mapped[int] = mapped_column(Integer, primary_key=True, autoincrement=True)
    name: Mapped[str] = mapped_column(String(255), nullable=False)
    slug: Mapped[str] = mapped_column(String(255), nullable=False)
    group: Mapped[str] = mapped_column(String(255), nullable=False)

    conditions: Mapped[List[PromotionCondition]] = relationship(
        back_populates="conditional",
        cascade="all, delete-orphan"
    )

    def __repr__(self) -> str:
        return f"<PromotionConditionConditional(name={self.name}, slug={self.slug})>"

    def __str__(self) -> str:
        return self.name


class PromotionConditionExpression(db.Model):
    __tablename__ = "promotion_condition_expression"

    id: Mapped[int] = mapped_column(Integer, primary_key=True, autoincrement=True)
    name: Mapped[str] = mapped_column(String(255), nullable=False)
    group: Mapped[str] = mapped_column(String(255), nullable=False)
    slug: Mapped[str] = mapped_column(String(255), nullable=False)
    expression: Mapped[str] = mapped_column(String(255), nullable=False)

    conditions: Mapped[List[PromotionCondition]] = relationship(
        back_populates="expression",
        cascade="all, delete-orphan"
    )

    def __repr__(self) -> str:
        return f"<PromotionConditionExpression(name={self.name}, slug={self.slug})>"

    def __str__(self) -> str:
        return self.name


class PromotionExceptionPeriod(db.Model):
    __tablename__ = "promotion_exception_period"

    id: Mapped[int] = mapped_column(Integer, primary_key=True, autoincrement=True)
    period_from: Mapped[datetime] = mapped_column(DateTime, nullable=False)
    period_to: Mapped[datetime] = mapped_column(DateTime, nullable=False)
    created_at: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow)
    updated_at: Mapped[datetime] = mapped_column(
        DateTime, default=datetime.utcnow, onupdate=datetime.utcnow
    )

    promotion_id: Mapped[Optional[int]] = mapped_column(
        Integer, ForeignKey("promotion.id", ondelete="SET NULL"), nullable=True
    )
    promotion: Mapped[Optional[Promotion]] = relationship(back_populates="exceptions")

    def __repr__(self) -> str:
        return f"<PromotionExceptionPeriod({self.period_from} -> {self.period_to})>"

    def __str__(self) -> str:
        return f"{self.period_from.strftime('%Y-%m-%d %H:%M:%S')} -> {self.period_to.strftime('%Y-%m-%d %H:%M:%S')}"


class Search(db.Model):
    __tablename__ = "search"

    id: Mapped[int] = mapped_column(Integer, primary_key=True, autoincrement=True)
    hotel: Mapped[str] = mapped_column(String(255), default="")
    city: Mapped[str] = mapped_column(String(255), default="")
    state: Mapped[Optional[str]] = mapped_column(String(255), nullable=True, default="")
    country: Mapped[str] = mapped_column(String(255), nullable=False)
    continent: Mapped[str] = mapped_column(String(255), nullable=False)
    region: Mapped[str] = mapped_column(String(255), nullable=False)
    search_code: Mapped[str] = mapped_column(String(255), nullable=False, unique=True)
    rooms: Mapped[int] = mapped_column(Integer, default=1)
    nights: Mapped[int] = mapped_column(Integer, nullable=False)
    check_in: Mapped[datetime] = mapped_column(DateTime, nullable=False)
    check_out: Mapped[datetime] = mapped_column(DateTime, nullable=False)
    room_1_adults: Mapped[int] = mapped_column(Integer, nullable=False)
    room_1_children: Mapped[int] = mapped_column(Integer, nullable=False)
    room_1_children_age: Mapped[Optional[List[int]]] = mapped_column(JSON, nullable=True)
    created_at: Mapped[datetime] = mapped_column(DateTime, default=func.now())
    updated_at: Mapped[datetime] = mapped_column(DateTime, default=func.now(), onupdate=func.now())

    user: Mapped[Optional["User"]] = relationship(back_populates="searches", lazy="joined")
    user_id: Mapped[Optional[int]] = mapped_column(ForeignKey("user.id"))

    # check_out_process_id: Mapped[Optional[int]] = mapped_column(ForeignKey("checkout.id"), nullable=True)
    # check_out_process: Mapped[Optional["CheckOut"]] = relationship(back_populates="search")

    def __repr__(self) -> str:
        return f"<Search(search_code={self.search_code}, hotel={self.hotel})>"


class User(db.Model):
    __tablename__ = 'user'

    # Primary Key with mapped_column
    id: Mapped[int] = mapped_column(Integer, primary_key=True, autoincrement=True)

    # Fields with Mapped typing and mapped_column
    name: Mapped[str] = mapped_column(String(255), nullable=False)
    lastname: Mapped[str] = mapped_column(String(255), nullable=False)
    is_superadmin: Mapped[bool] = mapped_column(Boolean, default=False)
    vip: Mapped[bool] = mapped_column(Boolean, default=False)
    agency_id: Mapped[int | None] = mapped_column(Integer, ForeignKey('agency.id'), nullable=True)

    # Relationships
    agency: Mapped["Agency"] = relationship('Agency', back_populates='users')
    searches: Mapped[list["Search"]] = relationship('Search', back_populates='user', cascade="all, delete-orphan")
    # check_outs: Mapped[list["CheckOut"]] = relationship('CheckOut', back_populates='user')
    # reservations: Mapped[list["Reservation"]] = relationship('Reservation', back_populates='operator')

    def __repr__(self):
        return f"<User(name={self.name}, lastname={self.lastname}, superadmin={self.superadmin}, vip={self.vip})>"
 

# Example of related models:
class Agency(db.Model):
    __tablename__ = 'agency'
    id: Mapped[int] = mapped_column(Integer, primary_key=True)
    name: Mapped[str] = mapped_column(String(255))
    commission: Mapped[Decimal] = mapped_column(Numeric(15, 2), nullable=False)
    users: Mapped[list[User]] = relationship('User', back_populates='agency')


