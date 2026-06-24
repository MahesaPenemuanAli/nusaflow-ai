from fastapi import APIRouter
from app.config import settings

router = APIRouter()

@router.get("/health", tags=["Health"])
def health_check():
    return {
        "success": True,
        "status": "healthy",
        "service": settings.app_name
    }
