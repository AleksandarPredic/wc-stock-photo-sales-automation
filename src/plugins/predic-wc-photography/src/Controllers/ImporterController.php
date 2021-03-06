<?php

namespace PredicWCPhoto\Controllers;

use PredicWCPhoto\Contracts\ControllerInterface;
use PredicWCPhoto\Contracts\ImporterInterface;

/**
 * Class ImporterController
 *
 * @package PredicWCPhoto\Controllers
 */
class ImporterController implements ControllerInterface
{

    /**
     * @var string
     */
    private $pluginSlug;

    /**
     * @var string
     */
    private $formAction;

    /**
     * @var string
     */
    private $formNonceName;

    /**
     * @var ImporterInterface
     */
    private $importer;

    /**
     * ImporterController constructor.
     *
     * @param ImporterInterface $importer
     */
    public function __construct(ImporterInterface $importer)
    {
        $this->pluginSlug    = predic_wc_photography_helpers()->config->getPluginSlug();
        $this->formAction    = str_replace('-', '_', sprintf('%s_import_form_action', $this->pluginSlug));
        $this->formNonceName = str_replace('-', '_', sprintf('%s_import_form_nonce_name', $this->pluginSlug));

        $this->importer = $importer;
    }

    public function init()
    {
        add_action(
            str_replace('-', '_', sprintf('%s_page_import', $this->pluginSlug)),
            [$this, 'form']
        );

        add_action(
            sprintf(
                'admin_post_%s',
                $this->formAction
            ),
            [$this, 'import']
        );
    }

    public function form()
    {
        printf(
            '<h1>%3$s</h1>
			<form method="post" action="%6$s" enctype="multipart/form-data">
				<input type="hidden" name="action" value="%4$s"/>
				%5$s
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row"><label for="input_id">%1$s</label></th>
							<td><input name="files[]" type="file" multiple/></td>
						</tr>
						<tr>
							<th scope="row"></th>
							<td><button type="submit" class="button button-primary">%2$s</button></td>
						</tr>
					</tbody>
				</table>
			</form>',
            esc_html__('Select photos', 'predic-wc-photography'),
            esc_html__('Import', 'predic-wc-photography'),
            esc_html__('Import photos and convert them to products', 'predic-wc-photography'),
            $this->formAction,
            wp_nonce_field(
                $this->formAction,
                $this->formNonceName,
                true,
                false
            ),
            esc_url(admin_url('admin-post.php'))
        );
    }

    public function import()
    {
    	$nonce = isset($_POST[$this->formNonceName]) ? $_POST[$this->formNonceName] : '';

		if ( ! wp_verify_nonce( $nonce, $this->formAction ) ) {
			die( esc_html__( 'Security check', 'predic-wc-photography' ) );
		}

        // TODO: make ajax method to call this for each uploaded image as we can't handle more than 10 images - safe way
        // and to avoid this Warning: Maximum number of allowable file uploads has been exceeded in Unknown on line 0

        if (! isset($_FILES['files']['name']) || empty($_FILES['files']['name'])) {
            throw new \Exception(
                esc_html__('No files selected!', 'predic-wc-photography'),
                400
            );
        }

        foreach ($_FILES['files']['type'] as $type) {
            if ('image/jpeg' !== $type) {
                throw new \Exception(
                    esc_html__('Only jpeg image types are supported!', 'predic-wc-photography'),
                    400
                );
            }
        }

        $photos = [];
        for ($i = 0; $i < count($_FILES['files']['name']); $i++) {
            $photos[] = [
                'filename' => $_FILES['files']['name'][$i],
                'path'     => $_FILES['files']['tmp_name'][$i]
            ];
        }

        // TODO: Add try cache
        $result = $this->importer->import($photos);
        var_dump($result);
        die();
    }
}
