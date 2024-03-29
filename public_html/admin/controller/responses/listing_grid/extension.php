<?php

/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2021 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

/**
 * Class ControllerResponsesListingGridExtension
 */
class ControllerResponsesListingGridExtension extends AController
{
    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('extension/extensions');
        /** @var ModelToolMPAPI $mpModel */
        $mpModel = $this->loadModel('tool/mp_api');

        $page = $this->request->post['page']; // get the requested page
        if ((int) $page < 0) {
            $page = 0;
        }
        $limit = $this->request->post['rows']; // get how many rows we want to have into the grid
        $sidx = $this->request->post['sidx']; // get index row - i.e. user click to sort
        $sord = $this->request->post['sord']; // get the direction

        $search_str = '';
        if (isset($this->request->post['_search']) && $this->request->post['_search'] == 'true') {
            $searchData = json_decode(htmlspecialchars_decode($this->request->post['filters']), true);
            $search_str = trim($searchData['rules'][0]['data']);
        }

        $store_id = (int) $this->config->get('config_store_id');
        if ($this->request->get_or_post('store_id')) {
            $store_id = $this->request->get_or_post('store_id');
        }

        //sort
        $allowedSort = array_merge(
            [
                1 => 'key',
                'name',
                'category',
                'date_modified',
                'e.status',
                'store_name',
            ],
            (array) $this->data['allowed_sort']
        );
        if (!in_array($sidx, $allowedSort)) {
            $sidx = 'date_modified';
        }

        $allowedDirection = [SORT_ASC => 'asc', SORT_DESC => 'desc'];
        if (!in_array($sord, $allowedDirection)) {
            $sord = 'asc';
        }

        //extensions that has record in DB but missing files
        $missing_extensions = $this->extensions->getMissingExtensions();

        $data = [
            'store_id'   => $store_id,
            'search'     => $search_str,
            'filter'     => $this->session->data['extension_filter'],
            'sort_order' => [$sidx, $sord],
        ];
        if ($this->config->get('config_store_id')) {
            $data['store_id'] = (int) $this->config->get('config_store_id');
        }
        //extensions list. NOTE: set "force" mode to get data from db
        $extensions = $this->extension_manager->getExtensionsList($data, 'force');

        $total = $extensions->total;
        if ($total > 0) {
            $total_pages = ceil($total / $limit);
        } else {
            $total_pages = 0;
        }

        $response = new stdClass();
        $response->rows = [];
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $total;
        $response->userdata = new stdClass();

        $i = 0;
        $for_push = $push = [];

        // get extensions for install
        $ready_to_install = $this->session->data['ready_to_install'];
        $to_install = $to_inst_keys = [];

        if (!has_value($this->session->data['extension_filter'])
            || $this->session->data['extension_filter'] == 'extensions'
        ) {
            if ($ready_to_install && is_array($ready_to_install)) {
                foreach ($ready_to_install as $pack) {
                    $to_install[$pack['extension_name']] =
                        [
                            'remote_install'    => true,
                            'key'               => $pack['extension_name'],
                            'name'              => $pack['download_name'],
                            'extension_version' => $pack['extension_version'],
                            'installation_key'  => $pack['installation_key'],
                            'date_modified'     => $pack['date_modified'],
                        ];
                    $to_inst_keys[] = $pack['extension_name'];
                }
            }
        }

        //filter already installed from remote list (ignores new versions too)
        foreach ($extensions->rows as $row) {
            if (in_array($row['key'], $to_inst_keys)) {
                unset($to_install[$row['key']]);
            }
        }

        $rows = array_merge($to_install, $extensions->rows);
        $updates = $this->session->data['extensions_updates'];

        //get popular extensions
        $popular = $mpModel->getPopularity();
        foreach ($rows as $row) {
            $extension = $row['key'];
            if (!$extension) {
                continue;
            }
            if (count(explode('.', $row['version'])) == 2) {
                $row['version'] .= '.0';
            }
            $expired = false;
            $response->rows[$i]['id'] = str_replace(' ', '-', $row['key']).'_'.(int) $row['store_id'];
            $id = $response->rows[$i]['id'];

            $response->userdata->extension_id[$id] = $extension;
            $response->userdata->store_id[$id] = $row['store_id'];
            $response->userdata->popular[$id] = [
                'status'       => (!empty($popular) && in_array($extension, array_keys($popular))),
                'description'  => $popular[$extension]
            ];

            $name = !isset($row['name']) ? trim($this->extensions->getExtensionName($extension)) : $row['name'];

            //for new extensions
            if ($row['remote_install']) {
                $response->userdata->installation_key[$id] = $row['installation_key'];
                $icon = '<img src="'.RDIR_TEMPLATE.'image/default_extension.png'.'" alt="" border="0" />';
                $category = '';
                $status = $this->language->get('text_ready_to_install');
                $response->userdata->classes[$id] = 'success disable-edit disable-delete '
                    .'disable-uninstall disable-install';
            } //missing extension (removed manually)
            elseif (in_array($extension, $missing_extensions)) {
                $response->userdata->classes[$id] = 'warning disable-edit disable-install '
                    .'disable-uninstall disable-remote-install';

                $icon = '<img src="'.RDIR_TEMPLATE.'image/default_extension.png'.'" alt="'.$extension.'" />';
                $name = sprintf($this->language->get('text_missing_extension'), $extension);
                $category = $status = '';
                // change it for show it in list first by default sorting
                $row['date_modified'] = date('Y-m-d H:i:s', time());
            } //corrupted extension
            elseif (!file_exists(DIR_EXT.$extension.'/main.php') || !file_exists(DIR_EXT.$extension.'/config.xml')) {
                $response->userdata->classes[$id] = 'warning disable-edit disable-install '
                    .'disable-uninstall disable-remote-install';
                $icon = '<img src="'.RDIR_TEMPLATE.'image/default_extension.png'.'" alt="'.$extension.'" />';
                $name = sprintf($this->language->get('text_broken_extension'), $extension);
                $category = $status = '';
                // change it for show it in list first by default sorting
                $row['date_modified'] = date('Y-m-d H:i:s', time());
            } else {
                if (!$this->config->has($extension.'_status')) {
                    $response->userdata->classes[$id] = 'disable-edit disable-uninstall disable-remote-install';
                    $status = $this->language->get('text_not_installed');
                } else {
                    $response->userdata->classes[$id] = 'disable-delete disable-install disable-remote-install';
                    $status = $this->html->buildCheckbox(
                        [
                            'name'  => $extension.'['.$extension.'_status]',
                            'value' => $row['status'],
                            'style' => 'btn_switch',
                        ]
                    );
                }

                $icon_ext_img_url = HTTPS_EXT.$extension.'/image/icon.png';
                $icon_ext_dir = DIR_EXT.$extension.'/image/icon.png';
                $icon = (is_file($icon_ext_dir)
                    ? $icon_ext_img_url
                    : RDIR_TEMPLATE.'image/default_extension.png');

                if (!$this->config->has($extension.'_status')) {
                    $icon = '<img src="'.$icon.'" alt="'.$extension.'"/>';
                } else {
                    $icon = '<a href="'.$this->html->getSecureURL(
                            'extension/extensions/edit',
                            $this->data['url'].'&extension='.$extension
                        )
                        .'">'
                        .'<img src="'.$icon.'" alt="'.$extension.'"/></a>';
                }

                $category = $row['category'];
                // if update available
                if (is_array($updates) && isset($updates[$extension])) {
                    if (version_compare($updates[$extension]['version'], $row['version'], '>')) {
                        if ($updates[$extension]['installation_key']) {
                            $update_now_url = $this->html->getSecureURL(
                                'tool/package_installer',
                                '&extension_key='.$updates[$extension]['installation_key']
                            );
                        } else {
                            $update_now_url = $updates[$extension]['url'];
                        }
                        $name = '<p class="alert-info">'
                            .$name
                            .'<br>'
                            .sprintf(
                                $this->language->get('text_update_available'),
                                $updates[$extension]['version'],
                                $update_now_url
                            )
                            .'</p>';
                        $push[] = $i;
                    }
                }
                //when support period expired
                if (($updates
                        && isset($updates[$extension]['support_expiration'])
                        && $updates[$extension]['support_expiration']
                        && dateISO2Int($updates[$extension]['support_expiration']) < time())
                    || //if no update info - takes date from extension table
                    ($row['support_expiration'] && dateISO2Int($row['support_expiration']) < time())
                    || //if extension purchased but store disconnected
                    ($row['license_key'] && !$this->config->get('mp_token'))
                ) {
                    $expired = true;
                    $response->userdata->classes[$id] = 'expired '.$response->userdata->classes[$id];
                }
                //check of minor cart version
                if (!is_null($this->config->get($extension."_status"))) {
                    $cfg = getExtensionConfigXml($extension);
                    if ($cfg->cartversions->item) {
                        $allSupportedCartVersions = (array) $cfg->cartversions->item;
                        $checks = isExtensionSupportsCart($allSupportedCartVersions);
                        if (!$checks['minor_check']) {
                            $name = '<p class="alert-info">'
                                .$name
                                .'<br>'
                                .$this->language->get('text_compatibility', 'extension/extensions_store')
                                .'</p>';
                        }
                    }
                }
            }

            if (!$expired) {
                $response->userdata->classes[$id] .= ' disable-expired ';
            }
            $response->userdata->mp_product_url[$id] = $row['mp_product_url'] ? : $updates[$extension]['url'];
            //if url of product page still empty - send to search result page
            if (!$response->userdata->mp_product_url[$id]) {
                $response->userdata->mp_product_url[$id] = $mpModel->getMPURL()
                    .'?rt=product/search&keyword='.$extension;
            }

            $response->rows[$i]['cell'] = [
                $icon,
                $name,
                $extension,
                $category,
                dateISO2Display($row['date_modified'], $this->language->get('date_format_short'))
            ];
            if (!$this->config->get('config_store_id')) {
                $response->rows[$i]['cell'][] = $row['store_name'] ? : $this->language->get('text_default');
            }
            $response->rows[$i]['cell'][] = $status;
            if ($push) {
                if (in_array($i, $push)) {
                    $for_push[] = $response->rows[$i];
                    unset($response->rows[$i]);
                }
            }
            $i++;
        }

        if ($push) {
            foreach ($for_push as $ext) {
                array_unshift($response->rows, $ext);
            }
        }

        $response->rows = array_slice($response->rows, (int) ($page - 1) * $limit, $limit);
        $this->data['response'] = $response;

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        $this->load->library('json');
        $this->response->setOutput(AJson::encode($this->data['response']));
    }

    public function update()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        if (!$this->user->canModify('listing_grid/extension')) {
            $error = new AError('');
            $error->toJSONResponse(
                'NO_PERMISSIONS_402',
                [
                    'error_text'  => sprintf(
                        $this->language->get('error_permission_modify'),
                        'listing_grid/extension'
                    ),
                    'reset_value' => true,
                ]
            );
            return;
        }

        $this->loadLanguage('extension/extensions');
        $store_id = $this->request->post_or_get('store_id');
        if ($this->session->data['current_store_id']) {
            $store_id = $this->session->data['current_store_id'];
        }

        if (empty($this->request->get['id'])) {
            foreach ($this->request->post as $ext => $val) {
                $val['store_id'] = $store_id;
                $this->extension_manager->editSetting($ext, $val);
            }
        } else {
            $val = $this->request->post;
            $val['store_id'] = $store_id;
            $val['one_field'] = true; // sign that we change only one setting
            $this->extension_manager->editSetting($this->request->get['id'], $val);
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        $this->load->library('json');
        if ($this->extension_manager->errors) {
            $error = new AError('');
            $error->toJSONResponse(
                'VALIDATION_ERROR_406',
                [
                    'error_text'  => '<br>'.implode('<br>', $this->extension_manager->errors),
                    'reset_value' => true,
                ]
            );
        }
    }

    public function license()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('extension/extensions');

        // first of all we need check dependencies
        $config = getExtensionConfigXml($this->request->get['extension']);
        $result = $this->extension_manager->validateDependencies($this->request->get['extension'], $config);
        $this->data = ['license_text' => '', 'error_text' => ''];
        if ($result) {
            // if all fine show license agreement
            if (file_exists(DIR_EXT.$this->request->get['extension']."/license.txt")) {
                $this->data['license_text'] = file_get_contents(
                    DIR_EXT.$this->request->get['extension']."/license.txt"
                );
                $this->data['license_text'] = htmlentities($this->data['license_text'], ENT_QUOTES, 'UTF-8');
                $this->data['license_text'] = nl2br($this->data['license_text']);
            }
        } else {
            $error_text = $this->extension_manager->errors;
            end($error_text);
            $this->data['error_text'] = current($error_text);
        }
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        $this->load->library('json');
        $this->response->setOutput(AJson::encode($this->data));
    }

    /**
     * method check is enabled dependants of extension presents
     */
    public function dependants()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('extension/extensions');

        $result = $this->extension_manager->getChildrenExtensions($this->request->get['extension']);
        if ($result) {
            $this->data['result'] = $result;
            $this->view->batchAssign($this->language->getASet('extension/extensions'));

            //update controller data
            $this->extensions->hk_UpdateData($this, __FUNCTION__);

            $this->view->batchAssign($this->data);
            $this->processTemplate('responses/extension/extensions_dependants_dialog.tpl');
        }
    }
}
