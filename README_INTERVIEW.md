# Skill Forge – Skill Gap Analytics

A web-based skill assessment prototype developed during a 24-hour hackathon.

## Core flow
1. User selects a skill/domain assessment.
2. The application collects answers and calculates domain scores.
3. The dashboard visualizes strengths and skill gaps.
4. A recommendation engine maps identified gaps to relevant learning resources.

## Technology
- HTML
- CSS
- JavaScript
- JSON/structured assessment data
- PHP + MySQL components for persistence

## Important implementation note
The current recommendation flow is implemented as client-side rule-based logic using structured skill data. It should not be described as an external generative-AI API integration unless an API-backed implementation is added.
