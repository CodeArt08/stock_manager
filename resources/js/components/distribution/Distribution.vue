<template>
<div :class="{ 'blur-background': isModalVisible }">

  <!-- Titre -->
  <h1>Gestion de stock de CISCO Brickaville</h1>

  <!-- Informations et Actions -->
  <div class="parent1">

    <button @click="openModal" class="ajoutk">
      <img src="/images/logos/plusvrai.png" width="25px" height="25px" />
      <span class="ajtk">AJOUT DE DISTRIBUTION</span>
    </button>



    <div class="total">
      <img src="/images/logos/all.png" width="35px" height="35px" />
      <span class="ajt">Total Stock: {{ totalCartons }}C + {{ totalPieces }}P</span>
    </div>
    <div class="total">
      <img src="/images/logos/total-fat.png" width="35px" height="35px" />
      <span class="ajt">Total distribuée: {{ totalCartonsDist }}C + {{ totalPiecesDist }}P</span>
    </div>
  </div>

  <!-- Liste des distributions -->
  <div>
    <h3>Liste des distributions</h3>
    <table class="table2">
      <thead>
        <tr>
          <th><span>Désignation</span></th>
          <th><span>ZAP</span></th>
          <th><span>Établissement</span></th>
          <th><span>Carton(s)</span></th>
          <th><span>Pièce(s)</span></th>
          <th><span>Total en pièce</span></th>
          <th><span>Date de distribution</span></th>
          <th><span>Actions</span></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="dist in dists" :key="dist.id" class="tr">
          <td>{{ dist.idStock }}</td>
          <td>{{ dist.idZap }}</td>
          <td>{{ dist.idEtab }}</td>
          <td>{{ dist.nbrCarton }}</td>
          <td>{{ dist.nbrPiece }}</td>
          <td>{{ calculateTotalPiece(dist.nbrCarton, dist.pc, dist.nbrPiece) }}</td>
          <td>{{ dist.dateDist }}</td>
          <td>
            <a class="delete" @click="confirmDelete(dist.id)">
              <img src="/images/logos/delete.png" alt="" width="30px" height="30px" />
            </a>
          </td>
        </tr>
      </tbody>
    </table>

    <div v-if="pagination" class="pagination2">
      <ul>
        <li v-for="(link, index) in pagination.links" :key="index" :class="{ 'active': link.active }">
          <button @click="fetchPage(link.url)" v-html="link.label" :disabled="!link.url"></button>
        </li>
      </ul>
    </div>

  </div>
</div>
  



  <div v-if="isModalVisible" class="modal-overlay">
      <ajout-dist @close="closeModal" @userAdded="onUserAdded"/>
  </div>



</template>

<script>
import Swal from 'sweetalert2';
import AjoutDist from './ajoutDist.vue';

export default {
  name: "DistributionList",
  components: { AjoutDist },
  data() {
    return {
      totalCartons: 0,
      totalPieces: 0,
      totalCartonsDist: 0,
      totalPiecesDist: 0,
      dists: [],
      pagination: {},
      isModalVisible: false,
    };
  },
  created() {
    this.fetchDistributions();
  },
  methods: {
    onUserAdded() {
            this.fetchDistributions();
        },
    openModal() {
      this.isModalVisible = true;
      this.$emit('toggle-blur', true);
    },
    closeModal() {
      this.isModalVisible = false;
      this.$emit('toggle-blur', false);
    },



    calculateTotalPiece(nbrCarton, pc, nbrPiece) {
  return (Number(nbrCarton) || 0) * (Number(pc) || 0) + (Number(nbrPiece) || 0);
},

    confirmDelete(distId) {
      Swal.fire({
        title: "Êtes-vous sûr?",
        text: "Voulez-vous vraiment annuler cette distribution?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Oui, supprimer!",
        cancelButtonText: "Annuler",
        dangerMode: true,
      }).then(result => {
        if (result.isConfirmed) {
          fetch(`/api/annuler-dist/${distId}`, { method: 'DELETE' })
            .then(() => {
              this.dists = this.dists.filter(dist => dist.id !== distId);
              Swal.fire("Supprimée!", "La distribution a été annulée.", "success");
              this.fetchDistributions();
            })
            .catch(error => {
              console.error("Erreur lors de la suppression de la distribution:", error);
              Swal.fire("Erreur", "Impossible d'annuler la distribution.", "error");
            });
        }
      });
    },
    fetchDistributions(pageUrl = "/api/distributions") {
      // Exemple d'appel pour obtenir les distributions depuis une API
      fetch(pageUrl)
        .then((response) => response.json())
        .then((data) => {
          this.dists = data.dists.data || [];
          this.totalCartons = data.totalCartons;
          this.totalPieces = data.totalPieces;
          this.totalCartonsDist = data.totalCartonsDist;
          this.totalPiecesDist = data.totalPiecesDist;
          this.pagination = data.dists || {};
        });
    },
    fetchPage(url) {
      if (url) {
        this.fetchDistributions(url);
      }
    },

  },

};
</script>
