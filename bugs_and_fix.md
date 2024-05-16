**Unable to create a signed JWT from the given configuration** (When using auth with JWT)
-> Install OpenSSL (set up env var [Windows])
-> create directory config/jwt/
-> create files (private.pem & public.pem)
-> execute commands 
    - "openssl genrsa -out config/jwt/private.pem 4096"
    - "openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem"
