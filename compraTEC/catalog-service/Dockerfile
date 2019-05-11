FROM python:3.7.3
WORKDIR /app
ADD requirements.txt /app
COPY ["app.py","schema.py", "models.py","./"]
RUN pip install -r requirements.txt
COPY . .
EXPOSE 5000
CMD ["python","app.py"]
