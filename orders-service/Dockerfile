FROM node:8

WORKDIR /usr/src/compraTEC/orders-service

COPY package*.json ./

RUN npm install

COPY . .

EXPOSE 8080
CMD[ "npm", "start" ]
