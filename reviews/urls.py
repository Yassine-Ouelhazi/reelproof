from django.urls import path
from . import views

urlpatterns = [
    path('', views.ReviewListView.as_view(), name='review-list'),
    path('create/', views.ReviewCreateView.as_view(), name='review-create'),
    path('<int:pk>/edit/', views.ReviewUpdateView.as_view(), name='review-edit'),
    path('<int:pk>/', views.ReviewDetailView.as_view(), name='review-detail'),
    path('<int:pk>/vote/', views.ReviewVoteView.as_view(), name='review-vote'),
]
