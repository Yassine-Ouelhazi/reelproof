from django.contrib.auth.mixins import LoginRequiredMixin
from django.urls import reverse_lazy
from django.views.generic import CreateView, DetailView, UpdateView
from django.shortcuts import get_object_or_404

from .forms import UserRegisterForm, UserSettingsForm
from .models import User


class RegisterView(CreateView):
    model = User
    form_class = UserRegisterForm
    template_name = 'accounts/register.html'
    success_url = reverse_lazy('login')


class ProfileDetailView(DetailView):
    model = User
    template_name = 'accounts/profile.html'
    slug_field = 'username'
    slug_url_kwarg = 'username'


class ProfileSettingsView(LoginRequiredMixin, UpdateView):
    model = User
    form_class = UserSettingsForm
    template_name = 'accounts/settings.html'
    success_url = reverse_lazy('profile-detail')

    def get_object(self):
        return self.request.user

    def get_success_url(self):
        return reverse_lazy('profile-detail', kwargs={'username': self.request.user.username})
