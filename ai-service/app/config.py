import os
from pydantic_settings import BaseSettings, SettingsConfigDict

class Settings(BaseSettings):
    app_name: str = os.getenv("APP_NAME", "NusaFlow AI Service")
    app_env: str = os.getenv("APP_ENV", "local")
    app_debug: bool = os.getenv("APP_DEBUG", "true").lower() == "true"
    api_version: str = os.getenv("API_VERSION", "v1")

    model_config = SettingsConfigDict(env_file=".env")

settings = Settings()
