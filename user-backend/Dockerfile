# Use an official Node.js runtime as a parent image
FROM node:18.18.0


# Set the working directory in the container
WORKDIR /app/

# Copy package.json and package-lock.json to the working directory
COPY package.json ./
# COPY ../package-lock.json ./

# Install application dependencies (you can skip the `--production` flag for development)
RUN npm install

CMD ["npm", "run", "production"]