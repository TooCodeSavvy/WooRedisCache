FROM nginx:latest

# Install necessary packages and modules
RUN apt-get update && \
    apt-get install -y nginx-extras
 
CMD ["nginx", "-g", "daemon off;"]
