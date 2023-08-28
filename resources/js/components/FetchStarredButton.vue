<template>
    <div>
      <b-button @click="fetchStarredRepos" variant="primary" :disabled="isFetching">
        {{ buttonText }}
      </b-button>
  
      <b-toast v-model="showErrorToast" :auto-hide="5000" variant="danger" no-fade>
        {{ errorMessage }}
      </b-toast>
  
      <div v-if="showStarredRepos">
        <h2>Your Starred Repositories:</h2>
        <ul>
          <li v-for="repo in starredRepos" :key="repo.id">{{ repo.name }}</li>
        </ul>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        isFetching: false,
        buttonText: 'Fetch Starred Repos',
        showStarredRepos: false,
        starredRepos: [],
        showErrorToast: false,
        errorMessage: '',
      };
    },
    methods: {
      async fetchStarredRepos() {
        if (!this.isFetching) {
          this.isFetching = true;
          this.buttonText = 'Getting your data...';
  
          try {
            const response = await axios.get('/starred-repos');
  
            // Handle the fetched data and display starred repositories
            this.starredRepos = response.data;
            this.showStarredRepos = true;
  
            this.buttonText = 'Fetch Starred Repos';
            this.isFetching = false;
          } catch (error) {
            console.error('Failed to fetch starred repositories:', error);
          if (error.response) {
            if (error.response.status === 401) {
              this.errorMessage = 'Your token is invalid or unauthorized.';
            } else {
              this.errorMessage = 'Error fetching starred repositories.';
            }
          } else {
            this.errorMessage = 'An error occurred while fetching data.';
          }
          this.showErrorToast = true;

          this.buttonText = 'Fetch Starred Repos';
          this.isFetching = false;
          }
        }
      },
    },
  };
  </script>