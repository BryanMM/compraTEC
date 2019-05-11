const { makeExecutableSchema } = require('graphql-tools');
const bodyParser = require('body-parser');
const compression = require('compression');
const cors = require('cors');
const express = require('express');
const graphqlExpress = require('express-graphql');
const morgan = require('morgan');

const { Order } = require('./db/db');
const resolvers = require('./schema/resolvers');
const typeDefs = require('./schema/schema');

const HOST = process.env.HOST || 'localhost';
const NODE_ENV = process.env.NODE_ENV || 'development';
const PORT = process.env.PORT || 3000;


(async () => {
  const app = express();
  app.use(compression());
  app.use(cors());
  app.use(morgan('common'));
  const schema = makeExecutableSchema({ typeDefs, resolvers });
  app.use('/', bodyParser.json(), graphqlExpress({ schema, graphiql: true }));
  // Init server
  app.listen(PORT, () => {
    console.log('> Server running on 3000/orders')
  });
})();
