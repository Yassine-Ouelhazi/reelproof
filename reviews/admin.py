from django.contrib import admin
from .models import Review, ReviewVote, PointsLedger


@admin.register(Review)
class ReviewAdmin(admin.ModelAdmin):
    list_display = ('title', 'product', 'user', 'rating', 'status')


@admin.register(ReviewVote)
class ReviewVoteAdmin(admin.ModelAdmin):
    list_display = ('review', 'user', 'type', 'created_at')


@admin.register(PointsLedger)
class PointsLedgerAdmin(admin.ModelAdmin):
    list_display = ('user', 'points', 'reason', 'created_at')
