<template>
  <div>


    <div class="Z2">
      <form @submit.prevent="submitForm">
        <div class="formulaire">
          <div>
            <h2 class="loginTitre"> Formulaire</h2>
          </div>
          <div class="form" style="display: none">
            <label class="form-label">idStock:</label>
            <input type="text" class="form-control" v-model="form.id" />
          </div>

          <div class="form">
            <label class="form-label">Nom du Stock:</label>
            <input type="text" class="form-control" v-model="form.stock" />
          </div>

          <div class="form">
            <label class="form-label">Nombre de carton:</label>
            <input type="number" class="form-control" v-model="form.nbrC" />
          </div>

          <div class="form">
            <label class="form-label">Nombre de pièce:</label>
            <input type="number" class="form-control" v-model="form.nbrP" />
          </div>

          <div class="form">
            <label class="form-label">nombre de pièce/carton:</label>
            <input type="number" class="form-control" v-model="form.pc" />
          </div>

          <div class="form">
            <label class="form-label">Date d'arrivée:</label>
            <input type="date" class="form-control" v-model="form.dateA" />
          </div>

          <div class="bestOf">
            <button class="btnAjout">MODIFIER</button>
          </div>

          <div class="end2" @click="annuler">
            <span>Annuler</span>
          </div>
        </div>
      </form>
    </div>
  </div>

</template>



<script>
import Swal from 'sweetalert2';
export default {
  name: "UpdateStock",
  props: {
    id: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      form: {
        id: '',
        stock: '',
        nbrC: '',
        nbrP: '',
        pc: '',
        dateA: '',
      },
      loading: true, // Utilisé pour attendre la récupération des données
    };
  },
  created() {
    this.fetchStock();
  },
  methods: {
    fetchStock() {
      fetch(`/api/update-stock/${this.id}`)
        .then(response => response.json())
        .then(data => {
          this.form = {
            id: data.id,
            stock: data.stock,
            nbrC: data.nbrCarton,
            nbrP: data.nbrPiece,
            pc: data.pc,
            dateA: data.dateArrivee,
          };
          console.log("Données récupérées:", this.form); // Vérifie le contenu de form
          this.loading = false; // Indique que le chargement est terminé
        })
        .catch(error => {
          console.error("Erreur lors de la récupération du stock:", error);
          this.loading = false;
        });
    },
    submitForm() {
      console.log("Contenu de form avant soumission:", this.form);
      fetch(`/api/update-stock/traitement`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
        },
        body: JSON.stringify(this.form),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
        this.statusMessage = data.success;
        this.errorMessage = "";
        Swal.fire("Succès", data.success, "success");

        this.$emit("userAdded");
        this.$emit('close');
        this.resetForm();
      } else if (data.error) {
        this.errorMessage = data.error;
        this.statusMessage = "";
        Swal.fire("Erreur", data.error, "error");
        this.$emit('close');
            this.resetForm();
      }
        })
        .catch((error) => console.error("Erreur:", error));
    },
    annuler() {
      this.$emit('close');

    },

  }
}

</script>
