FROM node:10.13-alpine
ENV NAME Orders
WORKDIR /usr/src/app
COPY ["package.json", "package-lock.json*", "npm-shrinkwrap.json*", "./"]
RUN npm install --production --silent && mv node_modules ../
EXPOSE 3000
COPY . .
CMD ["npm" , "start" ]
