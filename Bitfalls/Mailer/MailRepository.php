<?php
    namespace Bitfalls\Mailer;

    use Bitfalls\Objects\Result;

    /**
     * Class MailRepository
     * @package Bitfalls\Mailer
     */
    interface MailRepository
    {

        /**
         * @param BitfallsMessage $oMessage
         * @param null $sDate
         * @param int $iPriority
         * @return mixed
         */
        function queueEmail(BitfallsMessage $oMessage, $sDate = null, $iPriority = 0);

        /**
         * @param $mInput
         * @return mixed
         */
        function markAsSent($mInput);

        /**
         * @param $mInput
         * @return mixed
         */
        function deleteEmail($mInput);

        /**
         * @param $aSearchParams
         * @internal param $iRange
         * @internal param null $sDate
         * @internal param null $iPriority
         * @internal param null $sRecipient
         * @internal param null $sSender
         * @internal param array $aOther
         * @return Result
         */
        function searchArchiveQueue($aSearchParams);

        /**
         * @param $aSearchParams
         * @return mixed
         */
        function fetchMessageObjectsArrayFromQueue($aSearchParams);

        /**
         * @param $sRecipient
         * @param null $sSender
         * @return mixed
         */
        function lastContact($sRecipient, $sSender = null);

        /**
         * @param BitfallsMessage $oMessage
         * @return mixed
         */
        function getSavedUnsentMessageExists(BitfallsMessage $oMessage);
    }
