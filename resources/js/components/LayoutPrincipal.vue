<template>



  <div class="container">
    <div class="containt1">
      <img src="/images/logos/logoCisco.jpg" alt="" width="60" height="60" class="img" />

      <!-- Sidebar avec navigation Vue Router -->
      <div class="sidebar">
        <a v-for="(item, index) in sidebarItems" :key="index" :class="['f1', { 'active': activeIndex === index }]"
          @click.prevent="navigate(index, item.route)">
          <img :src="item.icon" width="22" height="22" class="img2" />
          <span class="span1">{{ item.label }}</span>
        </a>
      </div>


      <!-- Bouton de déconnexion -->
      <a class="exit" @click="logout">
        <img src="/images/logos/power.png" width="20" height="20" />
        <span class="logout">Déconnexion</span>
      </a>
    </div>

    <!-- Contenu du routeur -->
    <div class="containt2" style="
             background-image: url(/images/logos/backG.jpg);
             background-position: center; 
             background-repeat: no-repeat;
             background-size: cover">
      <router-view></router-view>
    </div>
  </div>

</template>

<script>
export default {
  name: "LayoutPrincipal",
  data() {
    return {
      sessionCheckInterval: null,
      showAlert: false,
      activeIndex: null,
      sidebarItems: [
        { label: 'Stock', icon: '/images/logos/down-arrow.png', route: '/' },
        { label: 'Distribution', icon: '/images/logos/paper-airplane.png', route: '/liste-dist' },
        { label: 'ZAP', icon: '/images/logos/location.png', route: '/liste-zap' },
        { label: 'Etablissement', icon: '/images/logos/bird.png', route: '/liste-etab' },
        { label: 'Historique', icon: '/images/logos/history.png', route: '/liste-historique' },
        { label: 'Utilisateurs', icon: '/images/logos/3237472.png', route: '/liste-user' }
      ]
    };

  },

methods: {
  navigate(index, route) {
        this.activeIndex = index;
        this.$router.push(route);
    },
    checkSessionExpiration() {
        const expiration = localStorage.getItem('expiration');
        const currentTime = Date.now();
        const returnBy = localStorage.getItem('returnBy');

        // Si la session est expirée ou le délai de retour dépassé
        if (expiration && currentTime > parseInt(expiration, 10)) {
            alert('Votre session a expiré. Vous allez être déconnecté.');
            this.logout();
        } else if (returnBy && currentTime > parseInt(returnBy, 10)) {
            alert('Votre délai pour revenir est dépassé. Vous allez être déconnecté.');
            this.logout();
        }
    },

    logout() {

        localStorage.removeItem('isAuthenticated');
        localStorage.removeItem('user');
        
        this.$router.push('/login');
    },
},

};
</script>

<style scoped>
@import "/css/base.css";
@import "/sweetalert2/sweetalert2.min.css";

/* Ajoutez ici des styles spécifiques à LayoutPrincipal si nécessaire */
</style>
