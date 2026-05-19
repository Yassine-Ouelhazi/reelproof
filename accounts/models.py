from django.contrib.auth.models import AbstractUser
from django.db import models


class User(AbstractUser):
    ROLE_CHOICES = [
        ('reviewer', 'Reviewer'),
        ('buyer', 'Buyer'),
        ('brand', 'Brand'),
    ]

    full_name = models.CharField(max_length=80, blank=True)
    bio = models.TextField(blank=True, null=True)
    avatar = models.CharField(
        max_length=255, default='uploads/avatars/default.png')
    role = models.CharField(
        max_length=20, choices=ROLE_CHOICES, default='buyer')
    points = models.PositiveIntegerField(default=0)
    credibility_score = models.PositiveSmallIntegerField(default=0)
    is_verified = models.BooleanField(default=False)
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)

    def __str__(self):
        return self.username
