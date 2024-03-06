<template>
  <nav class="sticky top-0">
    <div class="bg-white py-2">
      <div
        class="mx-auto flex max-w-6xl items-center justify-between rounded-2xl border border-green-600 py-4 px-6"
      >
        <router-link to="/" class="flex h-10 items-center justify-center gap-2">
          <img loading="lazy" class="h-full" src="/logo.png" alt="Logo" />
          <h1 class="font-brand text-3xl uppercase">Genesis</h1>
        </router-link>
        <div class="flex items-center justify-end gap-4">
          <router-link
            to="/"
            exact-active-class="active"
            class="text-xl font-semibold uppercase"
          >
            Home
          </router-link>
          <router-link
            to="/about"
            exact-active-class="active"
            class="text-xl font-semibold uppercase"
          >
            About
          </router-link>
          <router-link
            to="/contact"
            exact-active-class="active"
            class="text-xl font-semibold uppercase"
          >
            Contact
          </router-link>
          <router-link
            to="/contact"
            exact-active-class="active"
            class="rounded-xl border py-1 px-6 text-xl font-semibold uppercase"
          >
            Join
          </router-link>
        </div>
      </div>
    </div>
  </nav>
  <main>
    <router-view></router-view>
  </main>
  <footer>
    <div class="bg-white py-8">
      <div class="mx-auto grid max-w-6xl md:grid-cols-3">
        <div class=""></div>
        <div class="flex justify-center gap-4">
          <div class="space-y-3 text-right">
            <h3 class="text-lg font-semibold text-sky-600">Service</h3>
            <div class="grid gap-2">
              <router-link to="/" class="hover:text-sky-600">
                FCPS Part-I
              </router-link>
              <router-link to="/" class="hover:text-sky-600">
                FCPS Part-I
              </router-link>
              <router-link to="/" class="hover:text-sky-600">
                FCPS Part-I
              </router-link>
              <router-link to="/" class="hover:text-sky-600">
                FCPS Part-I
              </router-link>
              <router-link to="/" class="hover:text-sky-600">
                FCPS Part-I
              </router-link>
              <router-link to="/" class="hover:text-sky-600">
                FCPS Part-I
              </router-link>
            </div>
          </div>
          <div class="space-y-3 text-left">
            <h3 class="text-lg font-semibold text-sky-600">Company</h3>
            <div class="grid gap-2">
              <router-link to="/" class="hover:text-sky-600">
                Turms of Use
              </router-link>
              <router-link to="/" class="hover:text-sky-600">
                Privecy Policy
              </router-link>
              <router-link to="/about" class="hover:text-sky-600">
                About Us
              </router-link>
              <router-link to="/contact" class="hover:text-sky-600">
                Contact Us
              </router-link>
              <router-link to="/" class="hover:text-sky-600">
                FAQs
              </router-link>
            </div>
          </div>
        </div>
        <div class=""></div>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  components: {},
  data() {
    return {
      showNav: false,
    };
  },
};
</script>
