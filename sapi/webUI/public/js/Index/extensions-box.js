const get_content = async (url) => {
    const response = await fetch(url, {
        credentials: 'include',
        method: 'GET',
        mode: 'cors',
        headers: {
            'Access-Control-Request-Method': 'GET',
            'Access-Control-Request-Credentials': true,
            'Access-Control-Request-Private-Network': true
        }
    })
    return await response.json()
}

const show_all_extension_list_template = (all_extension_list, ready_extension_list) => {
    let children = '  '
    all_extension_list.map((value, index, array) => {
        children += `
                <li data-value="${value}">
                <label  for="ext_${value}" >
                 <input type="checkbox" id="ext_${value}" value="${value}" />
                 ${value}
                </label>
                </li>
                `
    })
    document.querySelector('#all_extentions').innerHTML = children
    ready_extension_list.map((value, index, array) => {
        let element = document.querySelector(`#ext_${value}`)
        if (element) {
            element.checked = true
        }
    })
    document.querySelector('#all_extentions').setAttribute(
        'data-default-ready-extension-list',
        JSON.stringify(ready_extension_list)
    )
}

const input_check_box_bind_event = () => {
    document.querySelector('#all_extentions').addEventListener('click', (event) => {
        if (event.target.nodeName === 'INPUT') {
            document.querySelector('.generate-cmd-button').click()
        }
        event.stopPropagation()
        // event.preventDefault();
    })

    const reset_button = document.querySelector('.reset-cmd-button')
    reset_button.addEventListener('click', (event) => {
        const default_ready_extension_list = JSON.parse(
            document.querySelector('#all_extentions')
                .getAttribute('data-default-ready-extension-list')
        )
        const button = event.target
        let checked = true
        if (button.getAttribute('data-status') === 'enable') {
            button.setAttribute('data-status', 'disable')
            button.innerText = '启用默认扩展'
            checked = false
        } else {
            button.setAttribute('data-status', 'enable')
            button.innerText = '停用默认扩展'
            checked = true
        }

        default_ready_extension_list.map((value) => {
            const element = document.querySelector(`#ext_${value}`)
            element.checked = checked
        })
        event.stopPropagation()
        event.preventDefault()
        document.querySelector('.generate-cmd-button').click()
    })
}

const show_extension_list = async () => {
    const [all_extension_list, ready_extension_list] = await Promise.all([
        get_content('/data/extension_list.json'),
        get_content('/data/default_extension_list.json')
    ])
    show_all_extension_list_template(all_extension_list, ready_extension_list)
    input_check_box_bind_event()
}

export {show_extension_list}
