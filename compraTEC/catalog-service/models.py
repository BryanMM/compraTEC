from datetime import datetime
from mongoengine import Document
from mongoengine.fields import (
    IntField, StringField,
)

class Catalog(Document):
    brand = StringField(required=True)
    meta = {'collection': 'catalog'}
    name = StringField(required=True)
    price = IntField(required=True)
    provider = StringField(required=True)
    stock = IntField(required=True)
    weight = StringField(required=True)
