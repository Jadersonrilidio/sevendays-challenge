<?php

/**
 * 
 */
function validatePostRegisterUser(array $inputData): array
{
    $validation = createRegisterValidationObject($inputData);

    checkForWhitespaceValues($validation);
    checkIfEmailAlreadyExists($validation);
    checkIfPasswordHasAtLeastTenChars($validation);
    checkIfPasswordsMatch($validation);
    assertInputsAreValid($validation);

    return $validation;
}

/**
 * 
 */
function validatePostLoginUser(array $inputData): array
{
    $validation = createLoginValidationObject($inputData);

    checkForWhitespaceValues($validation);
    assertInputsAreValid($validation);

    return $validation;
}

/**
 * 
 */
function createRegisterValidationObject(array $inputData): array
{
    $name = filter_var($inputData['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
    $email = filter_var($inputData['email'], FILTER_SANITIZE_EMAIL) ?? '';
    $password = filter_var($inputData['password'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
    $passwordConfirm = filter_var($inputData['password-confirm'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';

    return array(
        'name' => array(
            'value' => $name,
            'valid' => true,
            'errors' => []
        ),
        'email' => array(
            'value' => $email,
            'valid' => true,
            'errors' => []
        ),
        'password' => array(
            'value' => $password,
            'valid' => true,
            'errors' => []
        ),
        'password-confirm' => array(
            'value' => $passwordConfirm,
            'valid' => true,
            'errors' => []
        ),
        'status' => array(
            'valid' => true,
            'class' => 'mensagem-sucesso',
            'message' => "User sucessfully registered.<br>Please confirm your email before proceed."
        )
    );
}


/**
 * 
 */
function createLoginValidationObject(array $inputData): array
{
    $email = filter_var($inputData['email'], FILTER_SANITIZE_EMAIL) ?? '';
    $password = filter_var($inputData['password'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';

    return array(
        'email' => array(
            'value' => $email,
            'valid' => true,
            'errors' => []
        ),
        'password' => array(
            'value' => $password,
            'valid' => true,
            'errors' => []
        ),
        'status' => array(
            'valid' => true,
            'class' => 'mensagem-sucesso',
            'message' => "User logged in"
        )
    );
}

/**
 * 
 */
function checkForWhitespaceValues(array &$data): void
{
    foreach ($data as $field => $attributes) {
        if ($field !== 'status' and ctype_space($attributes['value'])) {
            $data[$field]['valid'] = false;
            $data[$field]['errors'][] = "Invalid $field";
        }
    }
}

/**
 * 
 */
function checkIfEmailAlreadyExists(array &$data): void
{
    if (email_exists($data['email']['value'])) {
        $data['email']['valid'] = false;
        $data['email']['errors'][] = 'Email already registered';
    }
}

/**
 * 
 */
function checkIfPasswordHasAtLeastTenChars(array &$data): void
{
    if (strlen($data['password']['value']) < 10) {
        $data['password']['valid'] = false;
        $data['password']['errors'][] = 'Password must have at least 10 characters';
    }
}

/**
 * 
 */
function checkIfPasswordsMatch(array &$data): void
{
    if ($data['password']['value'] !== $data['password-confirm']['value']) {
        $data['password']['valid'] = false;
        $data['password']['errors'][] = 'Passwords does not match';
        $data['password-confirm']['valid'] = false;
        $data['password-confirm']['errors'][] = 'Passwords does not match';
    }
}

/**
 * 
 */
function assertInputsAreValid(array &$data): void
{
    foreach ($data as $field) {
        if ($field['valid'] === false) {
            $data['status']['valid'] = false;
            $data['status']['class'] = 'mensagem-erro';
            $data['status']['message'] = 'Error: Please check your inputs.';
            return;
        }
    }
}

/**
 * 
 */
function fromRegister(array &$data): void
{
    if (isset($_GET['from']) and $_GET['from'] === 'register') {
        $data['status'] = array(
            'class' => 'mensagem-sucesso',
            'message' => "User sucessfully registered.<br>Please confirm your email before proceed."
        );
    }
}
