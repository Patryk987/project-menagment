class Api {

    static user_key;
    static base_link;

    set_user_key(key) {
        Api.user_key = key;
    }

    set_base_link(base_link) {
        Api.base_link = base_link;
    }

    set_api_key(api_key) {
        Api.api_key = api_key;
    }

    convert_data_to_string(data) {

        var prepare_query = '';

        Object.keys(data).forEach((key) => {
            if (prepare_query.length == 0) {
                prepare_query += "?";
            } else {
                prepare_query += "&";
            }
            prepare_query += key + "=" + data[key];

        });

        return prepare_query;
    }

    async get(endpoint, data = {}) {

        var prepare_query = this.convert_data_to_string(data);

        var link = Api.base_link + "/" + endpoint + prepare_query;

        const requestOptions = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Api-Key': Api.api_key,
                'User-Key': Api.user_key
            }
        };

        return fetch(link, requestOptions)
            .then((response) => response.json())
            .then((responseData) => {

                return responseData;

            })
            .catch(error => {

                console.warn(error)
                return { "status": false }

            });


    }

    post(endpoint, data) {

        var link = Api.base_link + "/" + endpoint;

        const requestOptions = {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Api-Key': Api.api_key,
                'User-Key': Api.user_key
            },
            body: JSON.stringify(data)
        };

        return fetch(link, requestOptions)
            .then((response) => response.json())
            .then((responseData) => {
                return responseData;
            })
            .catch(error => {
                console.warn(error)
                return { "status": false }
            });

    }

    put(endpoint, data) {

        var link = Api.base_link + "/" + endpoint;

        const requestOptions = {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Api-Key': Api.api_key,
                'User-Key': Api.user_key
            },
            body: JSON.stringify(data)
        };

        return fetch(link, requestOptions)
            .then((response) => response.json())
            .then((responseData) => {
                return responseData;
            })
            .catch(error => {
                console.warn(error)
                return { "status": false }
            });


    }

    delete(endpoint, data) {

        var prepare_query = this.convert_data_to_string(data);

        var link = Api.base_link + "/" + endpoint + prepare_query;

        const requestOptions = {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Api-Key': Api.api_key,
                'User-Key': Api.user_key
            }
        };

        return fetch(link, requestOptions)
            .then((response) => response.json())
            .then((responseData) => {
                return responseData;
            })
            .catch(error => {
                console.warn(error)
                return { "status": false }
            });

    }

}
