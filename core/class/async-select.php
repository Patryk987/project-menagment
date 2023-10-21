<?php


class AsyncSelect
{
    private $link;
    public function __construct($link)
    {
        $this->link = $link;
    }

    private function script()
    {
        $script = "

            <script>

                async function getData(url = '', data = {}) {
                    const response = await fetch(url, {
                        method: 'GET',
                        mode: 'cors',
                        cache: 'no-cache',
                        credentials: 'same-origin',
                        headers: {
                            'user_key': '" . $_SESSION['token'] . "',
                            'Content-Type': 'application/json',
                            'api_key': '481AF261E34647FE963E735F93871BD9'
                        },
                        redirect: 'follow',
                        referrerPolicy: 'no-referrer'
                    });
                    return response.json();
                }
                
                function parseData(data) {

                    var html = ``;
                    data.map(item => {
                        html += item.key;   
                    })
                    

                    return html;
                }

                document.querySelector('#find').addEventListener('input', async () => {
                    var input = document.querySelector('#find').value;
                    var link = '" . $this->link . "?value=' + input;

                    var response = await getData(link);
                    console.log(response);
                    // var html = parseData(response.data); 

                    // document.querySelector('#results_area').innerHTML = html;
                });
            </script>

        ";

        return $script;
    }

    private function create_html(): string
    {
        $html = "<div class='find_box'>";
        $html .= "<div class='text_area'>";
        $html .= "<input type='text' id='find' name='find'>";
        $html .= "</div>";
        $html .= "<div id='results_area'>";
        $html .= "</div>";
        $html .= "</div>";

        return $html;
    }

    public function load()
    {
        return $this->create_html() . $this->script();
    }


    public function __destruct()
    {
    }
}