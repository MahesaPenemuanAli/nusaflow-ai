from app.schemas.recommendation_schema import RecommendationRequest

class RecommendationService:
    @staticmethod
    def recommend_destinations(request: RecommendationRequest) -> list[dict]:
        recommended_list = []
        
        for candidate in request.candidates:
            # 1. Skip current destination
            if candidate.destination_id == request.current_destination_id:
                continue
                
            # 2. Skip inactive destinations
            if not candidate.is_active:
                continue
                
            # 3. Calculate crowd_score
            crowd_score = 0.0
            if candidate.max_capacity > 0:
                crowd_score = min(1.0, candidate.visitor_count / candidate.max_capacity)
                
            # Determine crowd level
            if crowd_score <= 0.30:
                crowd_level = "low"
                crowd_label = "Sepi"
            elif crowd_score <= 0.60:
                crowd_level = "moderate"
                crowd_label = "Normal"
            elif crowd_score <= 0.85:
                crowd_level = "high"
                crowd_label = "Ramai"
            else:
                crowd_level = "packed"
                crowd_label = "Sangat Ramai"
                
            # 5. Skip packed destinations if avoid_crowded is true
            if request.avoid_crowded and crowd_level == "packed":
                continue
                
            # 4. Calculate recommendation score
            score = 0.0
            reasons = []
            
            if request.preferred_category and candidate.category.lower() == request.preferred_category.lower():
                score += 0.35
                reasons.append("Kategori sesuai")
                
            if crowd_level in ["low", "moderate"]:
                score += 0.30
                reasons.append("Lebih sepi")
                
            if candidate.rating and candidate.rating >= 4.0:
                score += 0.20
                reasons.append("Rating tinggi")
                
            if candidate.distance_km is not None and candidate.distance_km <= 10.0:
                score += 0.15
                reasons.append("Jarak dekat")
                
            if not reasons:
                reasons.append("Alternatif menarik")
                
            reason_str = ", ".join(reasons) + "."
            
            recommended_list.append({
                "destination_id": candidate.destination_id,
                "name": candidate.name,
                "category": candidate.category,
                "recommendation_score": round(score, 2),
                "crowd_score": round(crowd_score, 2),
                "crowd_level": crowd_level,
                "crowd_label": crowd_label,
                "reason": reason_str.capitalize()
            })
            
        # 6. Sort by recommendation_score descending
        recommended_list.sort(key=lambda x: x["recommendation_score"], reverse=True)
        
        # 7. Limit results
        return recommended_list[:request.limit]
