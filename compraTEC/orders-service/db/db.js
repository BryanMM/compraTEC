const Sequelize = require('sequelize');

////Connect db
const sequelize = new Sequelize('postgres://jitxsjif:GnL5MazZMd2aw3j_BUvb4GYwcjLv0VXv@isilo.db.elephantsql.com:5432/jitxsjif');

// Table model
const Order = sequelize.define(
	'order',
	{
		id: {
			type: Sequelize.UUID,
			primaryKey: true,
			unique: true,
		},
		client: {
			type: Sequelize.STRING,
		},
		product: {
            type: Sequelize.STRING,
        },
        quantity: {
            type: Sequelize.INTEGER,
        },
	},
	{
		timestamps: false
	},
	{
		indexes: [
			{
				unique: true,
				fields: ['id'],
			},
		],
	}
);

// Export connection and model
module.exports = {
	db: sequelize,
	Order,
};
