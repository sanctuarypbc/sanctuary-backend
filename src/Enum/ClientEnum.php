<?php

namespace App\Enum;

/**
 * Class ClientEnum
 * @package App\Enum
 */
class ClientEnum
{
    const OTHER_TYPE = 'other';

    const CREATE_BOOKING_API_REQUIRED_FIELDS = [
        "client_id",
        "room_number",
        "facility_inventory_type_id"
    ];

    const CREATE_CLIENT_API_REQUIRED_FIELDS = [
        "first_name",
        "last_name",
        "total_dependents",
        "number_of_pets"
    ];

    const CLIENT_ENTITY_POSSIBLE_UPDATE_FIELDS = [
        "username" => "Username",
        "email" => "Email",
        "first_name" => "FirstName",
        "last_name" => "LastName",
        "phone" => "Phone",
        "dob" => "Dob",
        "gender" => "Gender"
    ];

    const GENDER_TYPES = [
        "male",
        "female"
    ];

    const RACE_TYPES = [
        "American Indian or Alaskan Native",
        "Asian",
        "Black/African American",
        "Native Hawaiian / Pacific Islander",
        "White",
        "Two or more races",
        "Prefer not to answer"
    ];

    const CLIENT_DETAIL_FIELDS = [
        "id" =>"Id",
        "age" => "Age",
        "total_dependents" => "TotalDependents",
        "pet_status" => "PetStatus",
        "phone_with_cellular_service" => "PhoneWithCellularService",
        "need_translator" => "NeedTranslator",
        "case_number" => "CaseNumber",
        "valid_id" => "ValidId",
        "abuser_location" => "AbuserLocation",
        "number_of_pets" => "NumberOfPets",
        "physically_disabled" => "PhysicallyDisabled",
        "need_medical_assistance" => "NeedMedicalAssistance",
        "contacted_family" => "ContactedFamily",
        "client_address"   =>"ClientAddress",
        "client_address_type" =>"ClientAddressType",
        "is_waitlisted" => "IsWaitlisted",
        "notes" => "Notes",
        "location" => "Location",
        "race" => "Race",
        "ethnicity" => "Ethnicity",
        "incident_zip_code" => "IncidentZipCode"
    ];

    const WELLCOME_MESSAGE_TO_CLIENT = "Welcome to Sanctuary. Please use link to go to our app.\n Link: ";
    const GOT_NEW_CLIENT_MESSAGE_TO_ADVOCATE = " has been assigned to you at Sanctuary. Please check your notifications";
    const GOT_NEW_FACILITY_MESSAGE_TO_ADVOCATE = " has been assigned the following facility: ";
}