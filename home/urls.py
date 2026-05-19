from django.urls import path
from . import views

urlpatterns = [
    path('', views.HomeIndexView.as_view(), name='home'),
    path('explore/', views.ExploreView.as_view(), name='explore'),
    path('search/', views.SearchView.as_view(), name='search'),
    path('category/<slug:slug>/', views.CategoryView.as_view(), name='home-category'),
    path('dashboard/', views.DashboardView.as_view(), name='dashboard'),
]
