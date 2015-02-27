<?php
namespace Application\Sonata\UserBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as SonataCRUDController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserAdminController
 * @package Application\Sonata\UserBundle\Controller
 */
class UserAdminController extends SonataCRUDController
{
    /**
     * User Import
     */
    public function userImportAction(Request $request)
    {
        if (false === $this->admin->isGranted('IMPORT')) {
            throw new AccessDeniedException();
        }

        $form = $this->createFormBuilder([])
            ->add('csvFile', 'file', [
                    'mapped' => false,
                    'label' => 'File'
                ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isValid()) {
            $csvFile = $form->get('csvFile')->getData();
            $count = $this->importCustomersCsv($csvFile->getRealPath());
            $this->addFlash('sonata_flash_success', sprintf('(%s) Registros importados', $count));

            return $this->redirect($this->generateUrl('admin_sonata_user_user_list'));
        }

        return $this->render('ApplicationSonataUserBundle:CRUD:user_import.html.twig', array(
                'base_template' => $this->getBaseTemplate(),
                'action' => 'user_import',
                'form'   => $form->createView()
            ));
    }

    /**
     * @param $filePath
     * @return int
     */
    private function importCustomersCsv($filePath)
    {
        $importedRows = 0;
        if (($file = fopen($filePath, "r")) !== FALSE) {
            while (($row = fgetcsv($file, 1000, ";")) !== FALSE) {

                $customerName = array_shift($row);
                $customerPw = array_shift($row);

                // Insert to db
                //@todo import rows to db

                $importedRows++;
            }
            fclose($file);
        }

        return $importedRows;
    }
}
