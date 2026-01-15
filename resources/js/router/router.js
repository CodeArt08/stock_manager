import { createRouter, createWebHistory } from 'vue-router';

import User from '../components/user/User.vue';
import Login from '../components/Login.vue';
import LayoutPrincipal from '../components/LayoutPrincipal.vue';

import Stock from '../components/stock/Stock.vue';
import ajoutStock from '../components/stock/ajoutStock.vue';
import updateStock from '../components/stock/updateStock.vue';

import Distribution from '../components/distribution/Distribution.vue';
import ajoutDist from '../components/distribution/ajoutDist.vue';

import Zap from '../components/zap/Zap.vue';
import ajoutZap from '../components/zap/ajoutZap.vue';
import updateZap from '../components/zap/updateZap.vue';

import Etablissement from '../components/etablissement/Etablissement.vue';
import listeEtab from '../components/etablissement/listeEtab.vue';

import Historique from '../components/historique/Historique.vue';
import histDist from '../components/historique/histDist.vue';

const routes = [
  {
    path: '/login',
    component: Login, 
  },
  {
    path: '/',
    component: LayoutPrincipal,
    redirect: '/login',
    children: [
      { path: '', component: Stock }, // Affiche Stock.vue en premier
      { path: 'liste-dist', component: Distribution },
      { path: 'liste-zap', component: Zap },
      { path: 'liste-etab', component: Etablissement },
      { path: 'liste-historique', component: Historique },
      { path: 'liste-user', component: User },
      {path: '/ajout-stock', component: ajoutStock},
      {path: '/ajout-zap', component: ajoutZap},
      {path: '/ajout-dist', component: ajoutDist},
    
      // {
      //   path: '/update-stock/:id',
      //   name: 'update-stock',
      //   component: updateStock,
      //   props: true, 
      // },
      // {
      //   path: '/update-zap/:id',
      //   name: 'update-zap',
      //   component: updateZap,
      //   props: true, 
      // },
      {
        path: '/etab/:id/:name',
        name: 'listeEtab',
        component: listeEtab,
        props: true, 
    },
    {
      path: '/histDist/:id',
      name: 'histDist',
      component: histDist,
      props: true,
    },
    
      
    ],
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  const isAuthenticated = localStorage.getItem('isAuthenticated') === 'true';

  if (to.path !== '/login' && !isAuthenticated) {
      next('/login'); 
  } else if (to.path === '/login' && isAuthenticated) {
      next('/'); 
  } else {
      next(); // Laisse passer toutes les autres navigations
  }
});



router.afterEach((to) => {
  if (to.path === '/login') {
    document.body.classList.add('bodyLogin');
  } else {
    document.body.classList.remove('bodyLogin');
  }
});


export default router;
