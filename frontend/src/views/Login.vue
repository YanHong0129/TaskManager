<template>
  <div>
    <h2>Login</h2>
    <input v-model="email" placeholder="Email" />
    <input v-model="password" type="password" placeholder="Password" />
    <button @click="handleLogin">Login</button>
    <p v-if="error" style="color: red">{{ error }}</p>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { login } from '../services/ApiService';

const router = useRouter();
const email = ref('');
const password = ref('');
const error = ref('');

const handleLogin = async () => {
  try {
    const res = await login(email.value, password.value);
    localStorage.setItem('token', res.data.token);
    router.push('/tasks');
  } catch (err) {
    error.value = 'Invalid credentials';
  }
};
</script>
