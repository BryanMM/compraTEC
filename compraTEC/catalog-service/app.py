from flask import Flask, abort, request
from flask_graphql import GraphQLView
from mongoengine import connect
from schema import schema

connect(host='mongodb://localhost/catalog-service')

app = Flask(__name__)
app.debug = True

@app.before_request
def limit_remote_addr():
    if request.remote_addr != '127.0.0.1':
        abort(403)  # Forbidden

app.add_url_rule(
  '/graphql',
  view_func=GraphQLView.as_view('graphql', schema=schema, graphiql=True)
)

if __name__ == '__main__':
  app.run()
