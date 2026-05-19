from django import forms
from django.contrib.auth.forms import UserCreationForm, UserChangeForm
from .models import User


class UserRegisterForm(UserCreationForm):
    class Meta:
        model = User
        fields = ['username', 'email', 'full_name',
                  'role', 'password1', 'password2']


class UserSettingsForm(UserChangeForm):
    password = None

    class Meta:
        model = User
        fields = ['email', 'full_name', 'bio', 'avatar']
