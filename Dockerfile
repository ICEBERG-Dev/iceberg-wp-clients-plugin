# Use an official Node.js runtime as the base image
FROM node:16

# Set the working directory in the container
WORKDIR /app

# Copy the package.json and package-lock.json to the container
COPY package*.json ./

# Install the dependencies
RUN npm install

# Copy the rest of the application code to the container
COPY . .

ARG DOCKER_CONTAINER_PORT
ENV DOCKER_CONTAINER_PORT=DOCKER_CONTAINER_PORT

ARG PGUSER
ENV PGUSER=PGUSER

ARG PGHOST
ENV PGHOST=PGHOST

ARG PGPASSWORD
ENV PGPASSWORD=PGPASSWORD

ARG PGPORT
ENV PGPORT=PGPORT


ARG MSQLIUSER
ENV MSQLIUSER=MSQLIUSER

ARG MSQLIHOST
ENV MSQLIHOST=MSQLIHOST

ARG MSQLIPASSWORD
ENV MSQLIPASSWORD=MSQLIPASSWORD

ARG MSQLIPORT
ENV MSQLIPORT=MSQLIPORT

# Specify the command to run the application
CMD ["npm", "run", "initCallback"]
