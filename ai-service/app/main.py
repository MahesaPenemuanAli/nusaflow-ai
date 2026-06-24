from fastapi import FastAPI
from app.routes import health_routes, prediction_routes, recommendation_routes, ml_prediction_routes
from app.config import settings

app = FastAPI(
    title=settings.app_name,
    description="AI service for tourism crowd prediction and alternative destination recommendation",
    version="0.1.0",
    docs_url="/docs",
    redoc_url="/redoc"
)

# Include Routers
app.include_router(health_routes.router)
app.include_router(prediction_routes.router)
app.include_router(recommendation_routes.router)
app.include_router(ml_prediction_routes.router)

@app.get("/")
def read_root():
    return {
        "success": True,
        "message": f"{settings.app_name} is running",
        "version": "0.1.0"
    }
