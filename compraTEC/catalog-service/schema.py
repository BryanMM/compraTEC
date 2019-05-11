import graphene
from bson import ObjectId
from graphene.relay import Node
from graphene_mongo import MongoengineConnectionField, MongoengineObjectType
from models import Catalog as CatalogModel

class Product(MongoengineObjectType):
    class Meta:
        model = CatalogModel

class CreateProduct(graphene.Mutation):
    class Arguments:
        name = graphene.String(required=True)
        brand = graphene.String(required=True)
        weight = graphene.String(required=True)
        price = graphene.Int(required=True)
        stock = graphene.Int(required=True)
        provider = graphene.String(required=True)

    model = graphene.Field(Product)

    def mutate(self, info, name, brand, weight, price, stock, provider):
        model = Product._meta.model(
            name=name, brand=brand, weight=weight, price=price, stock=stock, provider=provider)
        model.save(force_insert=True)
        return CreateProduct(model)

class DeleteProduct(graphene.Mutation):
    class Arguments:
        id = graphene.String(required=True)

    ok = graphene.Boolean()

    def mutate(self, info, id):
        model = CatalogModel.objects(id=ObjectId(id))
        ok = bool(model.delete())
        return DeleteProduct(ok)

class UpdateProduct(graphene.Mutation):
    class Arguments:
        id = graphene.String(required=True)
        name = graphene.String()
        brand = graphene.String()
        weight = graphene.String()
        price = graphene.Int()
        stock = graphene.Int()
        provider = graphene.String()

    ok = graphene.Boolean()

    def mutate(self, info, id, name=None, brand=None, weight=None, price=None, stock=None, provider=None):
        ok = True
        model = CatalogModel.objects(id=ObjectId(id))
        if name:
            model.update_one(name=name)
            ok = ok or bool(model.update_one(name=name))
        if brand:
            model.update_one(brand=brand)
            ok = ok or bool(model.update_one(brand=brand))
        if weight:
            model.update_one(weight=weight)
            ok = ok or bool(model.update_one(weight=weight))
        if price:
            model.update_one(price=price)
            ok = ok or bool(model.update_one(price=price))
        if stock:
            model.update_one(stock=stock)
            ok = ok or bool(model.update_one(stock=stock))
        if provider:
            model.update_one(provider=provider)
            ok = ok or bool(model.update_one(provider=provider))

        return UpdateProduct(ok)

class Mutation(graphene.ObjectType):
    create_product = CreateProduct.Field()
    delete_product = DeleteProduct.Field()
    update_product = UpdateProduct.Field()

class Query(graphene.ObjectType):
    all_products = graphene.List(Product)
    product_by_id = graphene.Field(Product, id=graphene.String(required=True))

    def resolve_all_products(self, info):
        return list(CatalogModel.objects.all())

    def resolve_product_by_id(self, info, id):
        prod = None
        for product in list(CatalogModel.objects.all()):
            if str(product.id) == str(id):
                prod = product
                break
        return prod

schema = graphene.Schema(query=Query, mutation=Mutation)
