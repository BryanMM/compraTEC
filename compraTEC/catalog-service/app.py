#!/usr/local/bin/python3
import os
from flask import Flask
from flask_graphql import GraphQLView
import mongoengine
from schema import schema
from pymongo import MongoClient

mongoengine.connect(host='mongodb://192.168.3.110/?replicaSet=rs/catalog-service')

app = Flask(__name__)
app.debug = True
app.add_url_rule(
    '/',
    view_func=GraphQLView.as_view('graphql', schema=schema, graphiql=True)
)

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)
