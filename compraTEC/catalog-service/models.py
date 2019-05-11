from datetime import datetime
from mongoengine import Document
from mongoengine.fields import (
    IntField, StringField,
)


class Catalog(Document):
  meta = {'collection': 'catalog'}
  name = StringField(required=True)
  brand = StringField(required=True)
  weight = StringField(required=True)
  price = IntField(required=True)
  stock = IntField(required=True)
  provider = StringField(required=True)
