<template>
<div>
      <div class="Z2">
        <form @submit.prevent="submitForm">
          <div class="formulaire">
            <div>
                <h2 class="loginTitre"> Formulaire</h2>
            </div>
            <div class="form">
              <label class="form-label">Designation:</label>
              <input type="text" class="form-control" v-model="form.stock" required />
            </div>

            <div class="form">
              <label class="form-label">Nombre de carton:</label>
              <input type="number" class="form-control" v-model="form.nbrC" required />
            </div>

            <div class="form">
              <label class="form-label">Nombre de pièce:</label>
              <input type="number" class="form-control" v-model="form.nbrP" required />
            </div>

            <div class="form">
              <label class="form-label">Nombre de pièce/carton:</label>
              <input type="number" class="form-control" v-model="form.pc" required />
            </div>

            <div class="form">
              <label class="form-label">Date d'arrivée:</label>
              <input type="date" class="form-control" v-model="form.dateA" required />
            </div>



            <div class="bestOf">
                <button class="btnAjout">AJOUTER</button>
            </div>

            <div class="end2"  @click="annuler">
                <span>Annuler</span>
            </div>
          </div>
        </form>

        <div v-if="status" class="alert">
          <p>{{ status }}</p>
        </div>
    </div>


  </div>
</template>

<script>
import Swal from 'sweetalert2';
export default {
  name: "AjoutStock",
  data() {
    return {
      form: {
        stock: "",
        nbrC: "",
        nbrP: "",
        pc: "",
        dateA: "",
      },
      status: null,
    };
  },
  methods: {
    submitForm() {
      const csrfToken = document.querySelector('meta[name="csrf-token"]');
      if (!csrfToken) {
        console.error("CSRF token not found.");
        return;
      }

      fetch("/api/ajout-stock/traitement", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": csrfToken.getAttribute("content"),
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
    resetForm() {
      this.form = { stock: "", nbrC: "", nbrP: "", pc: "", dateA: "" };
      this.status = null;
    },

    annuler() {
            this.$emit('close');
        },
  }

};
</script>

<style>
/* Ajoutez vos styles ici */
</style>