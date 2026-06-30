FROM python:3.11-slim

# Install system dependencies needed for OpenCV Headless
RUN apt-get update && apt-get install -y --no-install-recommends \
    libglib2.0-0 \
    && rm -rf /var/lib/apt/lists/*

# Set up user 1000 for Hugging Face Spaces compatibility
RUN useradd -m -u 1000 user
USER user
ENV HOME=/home/user \
    PATH=/home/user/.local/bin:$PATH

WORKDIR $HOME/app

# Copy requirements and install
COPY --chown=user:user requirements.txt .
RUN pip install --no-cache-dir --user -r requirements.txt

# Copy microservice code and model
COPY --chown=user:user . .

EXPOSE 7860
CMD ["gunicorn", "-b", "0.0.0.0:7860", "app:app", "--timeout", "120"]
