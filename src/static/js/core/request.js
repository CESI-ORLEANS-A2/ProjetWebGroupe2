/// <reference path="../../../types/App.d.ts" />

(function () {
	class Request {
		/**
		 * Effectue une requête HTTP GET vers l'URL spécifiée.
		 *
		 * @param {string} url - L'URL vers laquelle effectuer la requête.
		 * @param {Object} options - Les options de la requête (facultatif).
		 * @returns {Promise} Une promesse qui se résout avec la réponse de la requête.
		 */
		get(url, options = {}) {
			return this.request('GET', url, options);
		}
		/**
		 * Effectue une requête HTTP de type POST.
		 *
		 * @param {string} url - L'URL de la requête.
		 * @param {Object} options - Les options de la requête (facultatif).
		 * @returns {Promise} Une promesse qui se résout avec la réponse de la requête.
		 */
		post(url, options = {}) {
			return this.request('POST', url, options);
		}
		/**
		 * Effectue une requête HTTP de type PUT.
		 *
		 * @param {string} url - L'URL de la requête.
		 * @param {Object} options - Les options de la requête.
		 * @returns {Promise} - Une promesse qui se résout avec la réponse de la requête.
		 */
		put(url, options = {}) {
			return this.request('PUT', url, options);
		}
		/**
		 * Effectue une requête HTTP DELETE à l'URL spécifiée avec les options fournies.
		 *
		 * @param {string} url - L'URL à laquelle envoyer la requête DELETE.
		 * @param {Object} options - Les options de la requête (facultatif).
		 * @returns {Promise} Une promesse qui se résout avec la réponse de la requête.
		 */
		delete(url, options = {}) {
			return this.request('DELETE', url, options);
		}
		/**
		 * Effectue une requête de type PATCH vers l'URL spécifiée avec les options fournies.
		 *
		 * @param {string} url - L'URL vers laquelle effectuer la requête PATCH.
		 * @param {Object} [options={}] - Les options de la requête.
		 * @returns {Promise} - Une promesse qui se résout avec la réponse de la requête.
		 */
		patch(url, options = {}) {
			return this.request('PATCH', url, options);
		}
		/**
		 * Effectue une requête HTTP de type HEAD vers l'URL spécifiée.
		 *
		 * @param {string} url - L'URL vers laquelle effectuer la requête.
		 * @param {Object} options - Les options de la requête (facultatif).
		 * @returns {Promise} Une promesse qui se résout avec la réponse de la requête.
		 */
		head(url, options = {}) {
			return this.request('HEAD', url, options);
		}
		/**
		 * Effectue une requête OPTIONS vers l'URL spécifiée avec les options fournies.
		 *
		 * @param {string} url - L'URL vers laquelle effectuer la requête OPTIONS.
		 * @param {object} options - Les options de la requête.
		 * @returns {Promise} Une promesse qui se résout avec la réponse de la requête.
		 */
		options(url, options = {}) {
			return this.request('OPTIONS', url, options);
		}
		/**
		 * Établit une connexion en utilisant la méthode HTTP CONNECT.
		 *
		 * @param {string} url - L'URL de destination pour la connexion.
		 * @param {Object} options - Les options de la requête (facultatif).
		 * @returns {Promise} - Une promesse qui se résout avec la réponse de la requête.
		 */
		connect(url, options = {}) {
			return this.request('CONNECT', url, options);
		}
		/**
		 * Effectue une requête de type TRACE vers l'URL spécifiée avec les options fournies.
		 *
		 * @param {string} url - L'URL vers laquelle effectuer la requête TRACE.
		 * @param {Object} [options={}] - Les options de la requête.
		 * @returns {Promise} Une promesse qui se résout avec la réponse de la requête.
		 */
		trace(url, options = {}) {
			return this.request('TRACE', url, options);
		}
		/**
		 * Effectue une requête HTTP en utilisant la méthode spécifiée, l'URL et les options fournies.
		 *
		 * @param {string} method - La méthode HTTP à utiliser pour la requête (GET, POST, PUT, DELETE, etc.).
		 * @param {string} url - L'URL de la ressource à laquelle envoyer la requête.
		 * @param {Object} [options={}] - Les options supplémentaires pour la requête.
		 * @param {Object} [options.query] - Les paramètres de requête à ajouter à l'URL.
		 * @param {Object} [options.headers] - Les en-têtes HTTP à inclure dans la requête.
		 * @param {Object} [options.body] - Le corps de la requête (pour les méthodes POST, PUT, etc.).
		 *
		 * @returns {Promise} Une promesse qui se résout avec les données de la réponse si la requête réussit, ou se rejette avec les données de la réponse si la requête échoue.
		 */
		request(method, url, options = {}) {
			return new Promise(async (resolve, reject) => {
				try {
					// Ajouter les paramètres de la requête à l'URL si nécessaire.
					if (options.query) {
						const params = new URLSearchParams(options.query);
						url += '?' + params.toString();
					}

					// Effectuer la requête
					const response = await fetch(url, {
						method: method,
						headers: options.headers || {},
						body: options.body
							? typeof (options.body === 'string'
									? options.body
									: JSON.stringify(options.body))
							: null
					}).catch(reject);

					// Vérifier si la requête a réussi, puis résoudre ou rejeter la promesse en conséquence.
					if (response.ok) resolve(await response.json());
					else reject(await response.json());
				} catch (error) {
					reject(error);
				}
			});
		}
	}

	// Ajouter la classe `Request` à l'objet global `app`.
	window.app.request = new Request();
})();
