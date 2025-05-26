import { API_BASE_URL, getAuthHeaders } from '~/utils/api'

export const fetchEventAttendees = async (organizationSlug, eventId) => {
  try {
    const response = await $fetch(
      `${API_BASE_URL}/${organizationSlug}/events/${eventId}/attendees`,
      {
        headers: getAuthHeaders()
      }
    )
    return response
  } catch (error) {
    console.error('Error fetching attendees:', error)
    throw error
  }
}

export const deleteAttendee = async (organizationSlug, eventId, attendeeId) => {
  try {
    return await $fetch(
      `${API_BASE_URL}/${organizationSlug}/events/${eventId}/attendees/${attendeeId}`,
      {
        method: 'DELETE',
        headers: getAuthHeaders()
      }
    )
  } catch (error) {
    console.error('Error deleting attendee:', error)
    throw error
  }
}

// New method to fetch attendees for a specific event with auth
export const fetchEventAttendeesWithAuth = async (organizationSlug, eventId) => {
  try {
    const response = await $fetch(
      `${API_BASE_URL}/${organizationSlug}/events/${eventId}/attendees`,
      {
        headers: getAuthHeaders()
      }
    )
    return response || []
  } catch (error) {
    console.error('Error fetching attendees:', error)
    throw error
  }
}