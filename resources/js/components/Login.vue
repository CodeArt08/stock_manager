<template>

    <div class="loginParent">
        <form @submit.prevent="login" class="loginForm">
            <div>
                <h2 class="loginTitre">Connexion</h2>
            </div>
            <div class="best">
                <label for="username" class="loginLabel">Nom d'utilisateur</label>
                <div class="mdp">
                    <img src="/images/logos/user.png" width="17px" height="17px" />
                    <select v-model="form.username" class="loginInput" :class="{ placeholder: !form.username }"
                        required>
                        <option disabled value="">Entrez votre nom d'utilisateur</option>
                        <option v-for="user in users" :key="user.id" :value="user.nom">
                            {{ user.nom }}
                        </option>
                    </select>

                </div>

            </div>

            <div class="bestOftwo">
                <div class="best">
                    <label for="password" class="loginLabel">Mot de passe</label>
                    <div class="mdp2">
                        <img src="/images/logos/key.png" width="14px" height="14px" />
                        <input :type="showPassword ? 'text' : 'password'" id="password" class="loginInput"
                            placeholder="Entrez votre mot de passe" v-model="form.password" required />
                    </div>

                </div>

                <div class="bestOfone">
                    <input type="checkbox" id="show-password" v-model="showPassword" />
                    <label for="show-password" class="show">Afficher le mot de passe</label>
                </div>
            </div>

            <div class="bestOf">
                <button type="submit" class="loginButton">Se connecter</button>
            </div>

            <div class="end">
                <p class="oui">Veuillez entrer vos <span class="loginSpan">informations.</span></p>
            </div>
         
        </form>
    </div>


</template>

<script>
export default {
    data() {
        return {
            form: {
                username: "",
                password: "",
            },
            users: [],
            showPassword: false,
        };
    },
    created() {
        this.fetchUsers();
    },

    methods: {
        login() {
    const loginData = {
        username: this.form.username,
        password: this.form.password,
    };

    fetch('/api/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(loginData),
    })
        .then(async (response) => {
            if (response.ok) {
                const data = await response.json();

                // Enregistre la session comme active
                localStorage.setItem('isAuthenticated', 'true');
                localStorage.setItem('user', JSON.stringify(data.user));

                alert('Login successful! Redirecting...');
                this.$router.push('/');
            } else {
                const errorData = await response.json();
                alert(`Error: ${errorData.message}`);
            }
        })
        .catch((error) => {
            console.error('Erreur lors de la connexion:', error);
            alert('An error occurred. Please try again later.');
        });
},


        fetchUsers(pageUrl = '/api/users') {
            fetch(pageUrl)
                .then(response => response.json())
                .then(data => {
                    console.log(data); // Vérifiez la structure de `data` ici
                    this.users = data.users.data || [];
                })
                .catch(error => {
                    console.error('Erreur lors de la récupération des utilisateurs:', error);
                    alert('Failed to fetch users. Please try again later.');
                });
        },
    },
};
</script>
