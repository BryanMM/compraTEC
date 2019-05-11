#!/usr/local/bin/python3
from flask import Flask
from flask_graphql import GraphQLView
from schema import schema
from mongoengine import connect


connect(host='mongodb://db/catalog-service')

app = Flask(__name__)
app.debug = True

app.add_url_rule(
    '/',
    view_func=GraphQLView.as_view('graphql', schema=schema, graphiql=True)
)

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)
