from django.db import models
from django.conf import settings
from products.models import Product


class Review(models.Model):
    STATUS_CHOICES = [('published', 'Published'),
                      ('pending', 'Pending'), ('rejected', 'Rejected')]

    user = models.ForeignKey(settings.AUTH_USER_MODEL,
                             on_delete=models.CASCADE)
    product = models.ForeignKey(Product, on_delete=models.CASCADE)
    title = models.CharField(max_length=120)
    description = models.TextField(blank=True, null=True)
    video_path = models.CharField(max_length=255)
    thumbnail = models.CharField(max_length=255, blank=True, null=True)
    rating = models.PositiveSmallIntegerField(default=5)
    upvotes = models.PositiveIntegerField(default=0)
    downvotes = models.PositiveIntegerField(default=0)
    views = models.PositiveIntegerField(default=0)
    status = models.CharField(
        max_length=20, choices=STATUS_CHOICES, default='published')
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)

    def __str__(self):
        return f"{self.title} — {self.product.name}"


class ReviewVote(models.Model):
    TYPE_CHOICES = [('up', 'Upvote'), ('down', 'Downvote')]

    review = models.ForeignKey(
        Review, on_delete=models.CASCADE, related_name='votes')
    user = models.ForeignKey(settings.AUTH_USER_MODEL,
                             on_delete=models.CASCADE)
    type = models.CharField(max_length=10, choices=TYPE_CHOICES)
    created_at = models.DateTimeField(auto_now_add=True)

    class Meta:
        unique_together = ('review', 'user')


class PointsLedger(models.Model):
    user = models.ForeignKey(settings.AUTH_USER_MODEL,
                             on_delete=models.CASCADE)
    points = models.IntegerField()
    reason = models.CharField(max_length=120)
    reference_id = models.PositiveIntegerField(null=True, blank=True)
    created_at = models.DateTimeField(auto_now_add=True)
