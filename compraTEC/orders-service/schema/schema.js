module.exports = `
type Order {
    client: String!,
    id: String!
    product: String!,
    quantity: Int!
}

type Query {
  allOrders: [Order]
  order(product: String!): Order
}

type Mutation {

  createOrder(
      client: String!,
      product: String!,
      quantity: Int!
  ): Order

  updateOrder(
    client: String,
    id: ID!,
    product: String,
    quantity: Int
  ): Order

  deleteOrder(id: ID!): Order

}


type Schema {
  query: Query
  mutation: Mutation
}
`;
