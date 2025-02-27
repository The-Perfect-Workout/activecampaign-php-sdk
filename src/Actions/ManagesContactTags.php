<?php

namespace PerfectWorkout\ActiveCampaign\Actions;

use PerfectWorkout\ActiveCampaign\Resources\Tag;
use PerfectWorkout\ActiveCampaign\Resources\Contact;

trait ManagesContactTags
{
    use ImplementsActions;

    /**
     * Add tag to contact.
     *
     * @param \PerfectWorkout\ActiveCampaign\Resources\Contact $contact
     * @param \PerfectWorkout\ActiveCampaign\Resources\Tag $tag
     *
     * @return array
     */
    public function addTagToContact(Contact $contact, Tag $tag)
    {
        $data = [
            'contact' => $contact->id,
            'tag' => $tag->id,
        ];

        return $this->transformCollection(
            $this->post('contactTags', ['json' => ['contactTag' => $data]]),
            Tag::class
        );
    }

    /**
     * @param \PerfectWorkout\ActiveCampaign\Resources\Contact $contact
     * @param array $tags
     */
    public function addTagsToContact(Contact $contact, array $tags)
    {
        foreach ($tags as $tag) {
            if ($tag instanceof Tag) {
                $this->addTagToContact($contact, $tag);
            } elseif (is_string($tag)) {
                $this->addTagToContact($contact, $this->findOrCreateTag($tag));
            }
        }
    }
}
