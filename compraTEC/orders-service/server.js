const { makeExecutableSchema } = require('graphql-tools');
const bodyParser = require('body-parser');
const compression = require('compression');
const cors = require('cors');
const express = require('express');
const graphqlExpress = require('express-graphql');
const ipfilter = require('express-ipfilter').IpFilter
const morgan = require('morgan');
const resolvers = require('./schema/resolvers');
const typeDefs = require('./schema/schema');

const PORT = process.env.PORT || 2000;

(async () => {
  const app = express();

  app.use(cors());
  app.use(compression());
  app.use(morgan('common'));

  // Whitelist the following IPs
  var ips = ['::ffff:186.26.117.94'];
  // Create the server
  app.use(ipfilter(ips, { mode: 'allow' }))
  const schema = makeExecutableSchema({ typeDefs, resolvers });
  app.use('/orders', bodyParser.json(), graphqlExpress({ schema, graphiql: true }));

  // Init server
  app.listen(PORT, () => {
    console.log('> Server running on 2000/orders')
  });
})();
