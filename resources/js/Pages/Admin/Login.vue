<template>
  <div class="login-page">
    <div class="login-card">
      <div class="login-header">
        <img src="/logo.png" class="w-24 h-24 rounded-full mx-auto mb-4">
        <h1 class="brand-name-login">Picpic</h1>
        <p class="brand-tagline">kumpul mencerita</p>
        
        <div class="header-divider"></div>
        
        <h2 class="login-title">Administrator</h2>
        <p class="login-sub">Silakan login untuk mengelola kasir Picpic</p>
      </div>

      <!-- Flash Message -->
      <div v-if="$page.props.flash.error" class="flash-error">
        {{ $page.props.flash.error }}
      </div>

      <form @submit.prevent="submit" class="login-form">
        <div class="form-group">
          <label for="email">Email</label>
          <div class="input-wrap">
            <EnvelopeIcon class="h-5 w-5 icon-left" />
            <input
              v-model="form.email"
              type="email"
              id="email"
              placeholder="admin@picpic.com"
              required
              :class="{ 'has-error': form.errors.email }"
            />
          </div>
          <span v-if="form.errors.email" class="error-text">{{ form.errors.email }}</span>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <div class="input-wrap">
            <LockClosedIcon class="h-5 w-5 icon-left" />
            <input
              v-model="form.password"
              :type="showPass ? 'text' : 'password'"
              id="password"
              placeholder="••••••••"
              required
              :class="{ 'has-error': form.errors.password }"
            />
            <button type="button" class="toggle-pass" @click="showPass = !showPass">
              <EyeIcon v-if="!showPass" class="h-5 w-5" />
              <EyeSlashIcon v-else class="h-5 w-5" />
            </button>
          </div>
          <span v-if="form.errors.password" class="error-text">{{ form.errors.password }}</span>
        </div>

        <button
          type="submit"
          class="btn-login"
          :disabled="form.processing"
        >
          <span v-if="!form.processing">Login Dashboard</span>
          <span v-else>🔒 Sedang Verifikasi…</span>
        </button>
      </form>

      <div class="login-footer">
        <p>© 2026 Project Picpic — Advanced Coding</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import { 
  SparklesIcon, 
  EnvelopeIcon, 
  LockClosedIcon, 
  EyeIcon, 
  EyeSlashIcon 
} from '@heroicons/vue/24/outline';

const showPass = ref(false);

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const submit = () => {
  form.post(route('admin.authenticate'), {
    onFinish: () => form.reset('password'),
  });
};
</script>

<style scoped>
.login-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #7C6BC4;
  background: radial-gradient(circle at top right, #9B8FD4 0%, #7C6BC4 100%);
  padding: 20px;
  font-family: 'Plus Jakarta Sans', sans-serif;
}

.login-card {
  width: 100%;
  max-width: 400px;
  background: white;
  border-radius: 28px;
  padding: 40px 32px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
}

.login-header {
  text-align: center;
  margin-bottom: 32px;
}

.login-logo {
  width: 120px;
  height: 120px;
  object-fit: contain;
  margin: 0 auto 12px;
}

.brand-name-login {
  font-size: 28px;
  font-weight: 800;
  color: #7C6BC4;
  margin: 0;
  letter-spacing: -1px;
}

.brand-tagline {
  font-size: 14px;
  color: #6B7280;
  font-weight: 500;
  margin-top: 2px;
}

.header-divider {
  width: 40px;
  height: 3px;
  background: #F0EEFF;
  border-radius: 10px;
  margin: 20px auto;
}

.login-title {
  font-size: 20px;
  font-weight: 700;
  color: #1B1B1B;
  margin: 0 0 4px;
}

.login-sub {
  font-size: 13px;
  color: #9CA3AF;
  margin: 0;
}

.flash-error {
  background: #FEF2F2;
  border: 1px solid #FCA5A5;
  color: #B91C1C;
  padding: 12px 16px;
  border-radius: 12px;
  font-size: 13px;
  font-weight: 600;
  margin-bottom: 24px;
  text-align: center;
  animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
}

@keyframes shake {
  10%, 90% { transform: translate3d(-1px, 0, 0); }
  20%, 80% { transform: translate3d(2px, 0, 0); }
  30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
  40%, 60% { transform: translate3d(4px, 0, 0); }
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  font-size: 13px;
  font-weight: 700;
  color: #374151;
  margin-bottom: 8px;
}

.input-wrap {
  position: relative;
  display: flex;
  align-items: center;
}

.icon-left {
  position: absolute;
  left: 14px;
  color: #9CA3AF;
}

.input-wrap input {
  width: 100%;
  padding: 12px 42px;
  border: 2px solid #F3F4F6;
  background: #F9FAFB;
  border-radius: 14px;
  font-size: 14px;
  font-weight: 600;
  color: #1B1B1B;
  transition: all 0.2s;
  outline: none;
}

.input-wrap input:focus {
  border-color: #7C6BC4;
  background: white;
  box-shadow: 0 0 0 4px rgba(124, 107, 196, 0.1);
}

.input-wrap input.has-error {
  border-color: #EF4444;
}

.toggle-pass {
  position: absolute;
  right: 14px;
  background: none;
  border: none;
  color: #9CA3AF;
  cursor: pointer;
}

.error-text {
  font-size: 12px;
  color: #EF4444;
  margin-top: 6px;
  display: block;
  font-weight: 600;
}

.btn-login {
  width: 100%;
  padding: 14px;
  background: #7C6BC4;
  color: white;
  border: none;
  border-radius: 14px;
  font-size: 15px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
  margin-top: 10px;
  box-shadow: 0 4px 15px rgba(124, 107, 196, 0.3);
}

.btn-login:hover { background: #5A4DA0; transform: translateY(-1px); }
.btn-login:active { transform: translateY(0); }
.btn-login:disabled { opacity: 0.7; cursor: not-allowed; }

.login-footer {
  margin-top: 32px;
  text-align: center;
  font-size: 11px;
  color: #9CA3AF;
  font-weight: 500;
}
</style>
