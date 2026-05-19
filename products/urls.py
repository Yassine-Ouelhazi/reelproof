from django.urls import path
from . import views

urlpatterns = [
    path('', views.ProductListView.as_view(), name='product-list'),
    path('search/', views.ProductSearchView.as_view(), name='product-search'),
    path('category/<slug:slug>/',
         views.CategoryProductListView.as_view(), name='category-detail'),
    path('<slug:slug>/', views.ProductDetailView.as_view(), name='product-detail'),
]
