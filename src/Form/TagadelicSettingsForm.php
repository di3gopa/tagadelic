<?php

/**
 * @file
 * Contains \Drupal\tagadelic\Form\TagadelicForm.
 */

namespace Drupal\tagadelic\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\taxonomy\Entity\Vocabulary;

class TagadelicSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'tagadelic_form';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $options = array();
    $config = $this->config('tagadelic.settings');

    $vocabularies = Vocabulary::loadMultiple();
    foreach ($vocabularies as $vocabulary) {
      $options[$vocabulary->get('vid')] = $vocabulary->get('name');
    }

    $form["vocabularies"] = array(
      "#type" => "checkboxes",
      "#title" => $this->t('Vocabularies used in Tag Cloud'),
      "#options" => $options,
      "#default_value" => $config->get('tagadelic.vocabularies'),
    );

    $form = parent::buildForm($form, $form_state);
    return $form;
  }

  /**
   * {@inheritdoc}.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritdoc}.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('tagadelic.settings');
    $config->set('tagadelic.vocabularies', $form_state->getValue('vocabularies'));
    $config->save();
    return parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}.
   */
  protected function getEditableConfigNames() {
    return [
      'tagadelic.settings',
    ];
  }
}
