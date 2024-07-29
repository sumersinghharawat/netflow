# Use an official Node.js runtime as the base image
FROM node:18

# Set the working directory in the container
WORKDIR /react

# Copy the package.json and package-lock.json files to the container
COPY package*.json ./

# Install project dependencies
RUN npm install --quiet

# Copy the source code into the container
COPY . .

# # Expose the port on which the application will run
EXPOSE 3000

# Define the command to start the application
CMD [ "npm", "start" ]
